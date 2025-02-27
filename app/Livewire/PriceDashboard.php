<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CryptoPrice;
use Illuminate\Support\Facades\Log;
use App\Events\PriceUpdated;

class PriceDashboard extends Component
{
    public $prices = [];
    public $lastUpdate;
    public $connectionStatus = true;

    public function mount()
    {
        $this->loadInitialPrices();
    }

    public function loadInitialPrices()
    {
        try {
            $prices = CryptoPrice::query()
                ->latestBySymbol()
                ->get();

            if ($prices->isEmpty()) {
                $this->prices = [];
                return;
            }

            $this->prices = $prices->map(function ($price) {
                return [
                    'symbol' => $price->symbol,
                    'price' => (float) $price->price,
                    'previous_price' => (float) $price->previous_price,
                    'last_btc' => (float) $price->last_btc,
                    'lowest' => (float) $price->lowest,
                    'highest' => (float) $price->highest,
                    'daily_change_percentage' => (float) $price->daily_change_percentage,
                    'exchanges' => $price->exchanges,
                    'timestamp' => $price->created_at->toIso8601String(),
                    'formatted_time' => $price->created_at->format('H:i:s'),
                    'trend' => $price->trend
                ];
            })->keyBy('symbol')->toArray();

            $this->lastUpdate = now()->format('H:i:s');
        } catch (\Exception $e) {
            Log::error('Error loading initial prices', ['error' => $e->getMessage()]);
            $this->prices = [];
        }
    }

    public function getListeners()
    {
        return [
            "echo:prices,PriceUpdated" => 'handlePriceUpdate',
        ];
    }

    public function handlePriceUpdate($data)
    {
        event(new PriceUpdated(...$data));

        try {
            if (!isset($data['symbol'], $data['price'])) {
                Log::warning('Invalid price update data received', ['data' => $data]);
                return;
            }

            $symbol = $data['symbol'];

            // Update prices in real-time
            $this->prices[$symbol] = [
                'symbol' => $data['symbol'],
                'price' => $data['price'],
                'trend' => $data['trend'],
                'daily_change_percentage' => $data['daily_change_percentage'],
                'highest' => $data['highest'],
                'lowest' => $data['lowest'],
                'last_btc' => $data['last_btc'],
                'exchanges' => $data['exchanges'] ?? [],
            ];

            // Trigger AlpineJS update
            $this->emit('priceUpdated', $this->prices[$symbol]);
        } catch (\Exception $e) {
            Log::error('Error handling price update', ['exception' => $e]);
        }
    }

    public function render()
    {
        return view('livewire.price-dashboard');
    }
}
