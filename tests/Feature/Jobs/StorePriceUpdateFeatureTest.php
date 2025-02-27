<?php

namespace Tests\Feature\Jobs;

use App\Jobs\StorePriceUpdate;
use App\Models\CryptoPrice;
use App\Events\PriceUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Tests\TestHelper;

class StorePriceUpdateFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake(); // Fake events to verify that they are dispatched
    }


    public function test_stores_new_price_and_dispatches_event()
    {
        $data = $this->generatePriceMockData();
        
        // Dispatch the job
        (new StorePriceUpdate(...$data))->handle();

        $this->assertDatabaseHas('crypto_prices', [
            'symbol' => $data['symbol'],
            'price' => $data['price'],
            'last_btc' => $data['lastBtc'],
            'lowest' => $data['lowest'],
            'highest' => $data['highest'],
            'daily_change_percentage' => $data['dailyChange'],
            // 'exchanges' => $data['exchanges'],
        ]);

        // Assert that the event was broadcasted
        Event::assertDispatched(PriceUpdated::class, function ($event) use ($data) {
            return  
                $event->symbol === $data['symbol'] &&
                $event->price === $data['price'] &&
                $event->last_btc === $data['lastBtc'] &&
                $event->lowest === $data['lowest'] &&
                $event->highest === $data['highest'] &&
                $event->daily_change_percentage === $data['dailyChange'] &&
                $event->exchanges === $data['exchanges'];
        });
    }
}
