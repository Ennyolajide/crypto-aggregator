<?php

namespace App\Jobs;

use App\Models\CryptoPrice;
use App\Events\PriceUpdated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StorePriceUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $symbol,
        private readonly float $price,
        private readonly float $lastBtc,
        private readonly float $lowest,
        private readonly float $highest,
        private readonly float $dailyChange,
        private readonly string $date,
        private readonly array $exchanges
    ) {}

    public function handle(): void
    {
        try {
            DB::transaction(function () {
                // Get previous price for trend calculation
                $previousPrice = CryptoPrice::where('symbol', $this->symbol)
                    ->latest()
                    ->value('price');

                // Calculate trend
                $trend = $this->calculateTrend($this->price, $previousPrice);
                
                $update = CryptoPrice::create([
                    'symbol' => $this->symbol,
                    'price' => $this->price,
                    'previous_price' => $previousPrice,
                    'last_btc' => $this->lastBtc,
                    'lowest' => $this->lowest,
                    'highest' => $this->highest,
                    'daily_change_percentage' => $this->dailyChange,
                    'exchanges' => $this->exchanges,
                    'trend' => $trend,
                    'created_at' => Carbon::parse($this->date)
                ]);

                Log::info('Price update saved', [
                    'symbol' => $update->symbol,
                    'price' => $update->price,
                    'trend' => $trend,
                    'exchanges' => $update->exchanges
                ]);

                broadcast(new PriceUpdated(
                    symbol: $update->symbol,
                    price: $update->price,
                    previous_price: $update->previous_price,
                    last_btc: $update->last_btc,
                    lowest: $update->lowest,
                    highest: $update->highest,
                    daily_change_percentage: $update->daily_change_percentage,
                    exchanges: $update->exchanges,
                    trend: $update->trend,
                    timestamp: $update->created_at
                ));
            });
        } catch (\Exception $e) {
            Log::error('Failed to store/broadcast price update', [
                'error' => $e->getMessage(),
                'symbol' => $this->symbol
            ]);
            throw $e;
        }
    }

    private function calculateTrend(?float $currentPrice, ?float $previousPrice): string
    {
        if ($previousPrice === null || $currentPrice == $previousPrice) {
            return 'neutral';
        }
        return $currentPrice > $previousPrice ? 'up' : 'down';
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<string>
     */
    public function tags(): array
    {
        return ['price_update', "symbol:{$this->symbol}"];
    }
}