<?php

namespace Tests\Feature;

use App\Jobs\StorePriceUpdate;
use App\Models\CryptoPrice;
use App\Events\PriceUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class StorePriceUpdateFeatureTest extends TestCase
{
    use RefreshDatabase, RefreshDatabase;

    public function test_stores_price_and_broadcasts_update()
    {
        Event::fake();

        $job = new StorePriceUpdate(
            symbol: 'BTCUSDT',
            price: 50000.00,
            lastBtc: 1.0,
            lowest: 49000.00,
            highest: 51000.00,
            dailyChange: 2.5,
            exchanges: ['binance', 'mexc'],
            date: now()->toDateTimeString()
        );

        $job->handle();

        $this->assertDatabaseHas('crypto_prices', [
            'symbol' => 'BTCUSDT',
            'price' => 50000.00,
            'last_btc' => 1.0,
            'lowest' => 49000.00,
            'highest' => 51000.00,
            'daily_change_percentage' => 2.5,
        ]);

        Event::assertDispatched(PriceUpdated::class, function ($event) {
            return $event->symbol === 'BTCUSDT' &&
                   $event->price === 50000.00 &&
                   $event->exchanges === ['binance', 'mexc'];
        });
    }

    public function test_handles_duplicate_price_updates()
    {
        Event::fake();

        $job = new StorePriceUpdate(
            symbol: 'BTCUSDT',
            price: 50000.00,
            lastBtc: 1.0,
            lowest: 49000.00,
            highest: 51000.00,
            dailyChange: 2.5,
            exchanges: ['binance'],
            date: now()->toDateTimeString()
        );

        // Execute twice
        $job->handle();
        $job->handle();

        $this->assertEquals(2, CryptoPrice::count());
        Event::assertDispatchedTimes(PriceUpdated::class, 2);
    }
} 