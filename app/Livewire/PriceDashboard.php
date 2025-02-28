<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

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
            $response = Http::get(route('api.prices.latest')); // Fetch from your API
            $data = $response->json();

            $this->prices = collect($data['prices'])->keyBy('symbol')->toArray();
            $this->lastUpdate = now()->toTimeString();
        } catch (\Exception $e) {
            Log::error("Error fetching initial prices: " . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.price-dashboard', [
            'prices' => $this->prices,
            'lastUpdate' => $this->lastUpdate,
        ]);
    }
}
