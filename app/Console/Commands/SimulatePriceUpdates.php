<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\PriceUpdated;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SimulatePriceUpdates extends Command
{
    protected $signature = 'simulate:prices';
    protected $description = 'Simulate real-time price updates for multiple symbols continuously';

    protected $symbols = ['BTC', 'ETH', 'XRP', 'ADA', 'DOGE']; // List of symbols
    protected $previousPrices = []; // Track previous prices per symbol

    public function handle()
    {
        // Initialize prices for each symbol
        foreach ($this->symbols as $symbol) {
            $this->previousPrices[$symbol] = round(mt_rand(1000, 60000) + mt_rand(0, 99) / 100, 2);
        }

        while (true) {
            foreach ($this->symbols as $symbol) {
                $this->simulatePriceUpdate($symbol);
                
                // Small delay to distribute updates instead of bulk updates
                usleep(500000); // 0.5 seconds delay per symbol
            }
        }
    }

    protected function simulatePriceUpdate($symbol)
    {
        $previousPrice = $this->previousPrices[$symbol];

        // Random fluctuation based on % of previous price
        $priceChange = $previousPrice * (mt_rand(-100, 100) / 500); // ±0.2% to ±20%
        $newPrice = round($previousPrice + $priceChange, 2);

        // Ensure price remains realistic
        $newPrice = max($newPrice, 0.01);

        $trend = $newPrice > $previousPrice ? 'up' : 'down';

        $lowest = round($newPrice * (1 - mt_rand(1, 5) / 100), 2);
        $highest = round($newPrice * (1 + mt_rand(1, 5) / 100), 2);
        $lastBtc = round(mt_rand(1, 100) / 100000, 8);
        $dailyChangePercentage = round((($newPrice - $previousPrice) / $previousPrice) * 100, 2);

        // Update stored previous price
        $this->previousPrices[$symbol] = $newPrice;

        event(new PriceUpdated(
            symbol: $symbol,
            price: (float) $newPrice,
            previous_price: (float) $previousPrice,
            last_btc: (float) $lastBtc,
            lowest: (float) $lowest,
            highest: (float) $highest,
            daily_change_percentage: (float) $dailyChangePercentage,
            exchanges: ['Binance', 'Coinbase', 'Kraken'],
            trend: $trend,
            timestamp: Carbon::now()
        ));

        Log::info("Price update sent: $symbol - New Price: $newPrice - Trend: $trend");
    }
}
