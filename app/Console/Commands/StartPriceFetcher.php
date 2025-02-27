<?php

namespace App\Console\Commands;

use App\Jobs\PriceFetcherJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputOption;

class StartPriceFetcher extends Command
{
    protected $signature = 'crypto:fetch-prices 
                          {--interval=5 : Interval in seconds between price updates}
                          {--symbols=* : Specific symbols to fetch (default: all configured symbols)}';

    protected $description = 'Start the price fetcher process for cryptocurrency prices';

    public function handle(): int
    {
        $interval = $this->option('interval');
        //If no specific symbols provided, use configured ones
        $symbols = $this->option('symbols') ?: config('crypto.pairs');
    

        // Validate configuration
        if (empty($symbols)) {
            $this->error('No symbols configured. Please check your configuration.');
            return 1;
        }

        if (empty(config('crypto.api_key'))) {
            $this->error('API key not configured. Please check your configuration.');
            return 1;
        }

        if (empty(config('crypto.exchanges', ['binance','mexc','huobi']))) {
            $this->error('No exchanges configured. Please check your configuration.');
            return 1;
        }

        try {
            $this->info('Starting price fetcher with the following configuration:');
            $this->table(
                ['Setting', 'Value'],
                [
                    ['Symbols', implode(', ', $symbols)],
                    ['Interval', $interval . ' seconds'],
                    ['Exchanges', implode(', ', config('crypto.exchanges', []))],
                ]
            );

            // Update the interval in the config
            config(['crypto.update_interval' => $interval]);

            // Dispatch the initial job
            PriceFetcherJob::dispatch($symbols)->onQueue('prices');

            $this->info('Price fetcher started successfully.');

            Log::info('Price fetcher started', [
                'symbols' => $symbols,
                'interval' => $interval,
                'exchanges' => config('crypto.exchanges')
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error('Failed to start price fetcher: ' . $e->getMessage());
            Log::error('Failed to start price fetcher', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['interval', 'i', InputOption::VALUE_OPTIONAL, 'Interval in seconds between price updates', 5],
            ['symbols', 's', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'Specific symbols to fetch', []],
        ];
    }
}