<?php

namespace Tests\Feature\Jobs;

use App\Jobs\PriceFetcherJob;
use App\Contracts\FreeCryptoApiClientInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FakeCryptoApiClient implements FreeCryptoApiClientInterface
{
    public function getPrices(array $symbols): array
    {
        return [
            [
                'symbol' => $symbols[0],
                'price' => 100.0,
                'last_btc' => 1.0,
                'lowest' => 90.0,
                'highest' => 110.0,
                'daily_change_percentage' => 5.0,
                'exchanges' => ['binance'],
                'timestamp' => now()->toDateTimeString(),
            ]
        ];
    }
}

class PriceFetcherJobInterfaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_uses_interface()
    {
        $job = new PriceFetcherJob(['BTCUSDT']);
        $client = new FakeCryptoApiClient();

        // Should not throw and should process prices
        $job->handle($client);

        $this->assertDatabaseHas('crypto_prices', [
            'symbol' => 'BTCUSDT',
            'price' => 100.0,
        ]);
    }
} 