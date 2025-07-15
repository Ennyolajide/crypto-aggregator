<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Contracts\FreeCryptoApiClientInterface;

/**
 * - SOLID (Single Responsibility): This job only fetches prices and dispatches StorePriceUpdate jobs (handle()), with scheduling in scheduleNextRun(). This makes the job focused, maintainable, and easy to test.
 * - SOLID (Open/Closed): The job depends on FreeCryptoApiClientInterface, so new price fetcher implementations can be injected without modifying this class.
 * - SOLID (Dependency Inversion): The job depends on an abstraction (FreeCryptoApiClientInterface), not a concrete class, making it flexible and decoupled from specific implementations.
 * - DRY: The scheduling and retry logic is encapsulated in scheduleNextRun() and the backoff array, avoiding repetition.
 * - YAGNI: No extra abstraction for job chaining or advanced scheduling; only the required logic for periodic price fetching is implemented.
 */
class PriceFetcherJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;
    public $maxExceptions = 3;
    public $backoff = [10, 30, 60]; // Backoff intervals for retries

    public function __construct(
        private readonly array $symbols
    ) {
        if (empty($this->symbols)) {
            throw new \InvalidArgumentException('No symbols provided');
        }
    }

    public function handle(FreeCryptoApiClientInterface $client): void
    {
        try {
            
            // Fetch prices from all exchanges in parallel
            $prices = $client->getPrices($this->symbols);

            if (empty($prices)) {
                Log::warning('No prices returned from API', ['symbols' => $this->symbols]);
                $this->scheduleNextRun();
                return;
            }

            Log::info('Fetched prices', ['symbols' => $this->symbols]);

            // Dispatch StorePriceUpdate job for each price
            foreach ($prices as $priceData) {
                StorePriceUpdate::dispatch(
                    symbol: $priceData['symbol'],
                    price: (float)$priceData['price'],
                    lastBtc: (float)$priceData['last_btc'],
                    lowest: (float)$priceData['lowest'],
                    highest: (float)$priceData['highest'],
                    dailyChange: (float)$priceData['daily_change_percentage'],
                    exchanges: $priceData['exchanges'],
                    date: $priceData['timestamp']
                );

                Log::info('Dispatched price update', [
                    'symbol' => $priceData['symbol'],
                    'price' => $priceData['price'],
                    'exchanges' => $priceData['exchanges']
                ]);
            }

            // Schedule next run
            $this->scheduleNextRun();

        } catch (\Exception $e) {
            Log::error('Price fetcher error', [
                'error' => $e->getMessage(),
                'symbols' => $this->symbols
            ]);

            // Check if the job should fail
            if ($this->attempts() >= $this->tries) {
                $this->fail($e);
                return;
            }

            // Release the job with a backoff delay
            $delay = $this->backoff[$this->attempts() - 1] ?? $this->backoff[count($this->backoff) - 1];
            $this->release($delay);
        }
    }

    private function scheduleNextRun(): void
    {
        self::dispatch($this->symbols)
            ->onQueue('prices')
            ->delay(now()->addSeconds(config('crypto.update_interval', 5)));
    }
}
