<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CryptoPrice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\CryptoPriceResource;

/**
 * - SOLID (Single Responsibility): This component only displays and updates price data in the UI (fetchInitialPrices(), getUpdatedPrices()). This makes the component easy to reason about and maintain.
 * - DRY: Data fetching and caching logic are encapsulated and reused for both initial and updated prices.
 * - YAGNI: Only implements UI and data logic required for the dashboard; no extra features or abstractions.
 */
class PriceDashboard extends Component
{
    public $prices = [];
    public $lastUpdate;


    protected $listeners = ['priceUpdated' => 'handlePriceUpdate'];

    public function mount()
    {
        $this->fetchInitialPrices();
    }

    public function fetchInitialPrices()
    {
        try {
            //TODO
            //fix issues with api url access 
            $response = Http::get(route('api.prices.latest')); // Fetch from your API
            $data = $response->json();
        } catch (\Exception $e) {
            $data = $this->getUpdatedPrices();
            //Log::error("Error fetching initial prices: " . $e->getMessage());
        } finally {
            $this->prices = collect($data['prices'])->keyBy('symbol')->toArray();
            $this->lastUpdate = now()->toTimeString();
        }
    }

    public function getUpdatedPrices()
    {
        $prices = Cache::remember('latest-crypto-prices', 30, function () {
            return CryptoPrice::query()
                ->latestBySymbol()
                ->get();
        });

        return [
            'prices' => $prices,
            'lastUpdate' => now()->toIso8601String()
        ];
    }

    public function render()
    {
        return view('livewire.price-dashboard', [
            'prices' => $this->prices,
            'lastUpdate' => $this->lastUpdate,
        ]);
    }
}
