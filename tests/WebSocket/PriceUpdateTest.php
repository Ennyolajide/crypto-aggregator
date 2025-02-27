<?php

namespace Tests\WebSocket;

use App\Events\PriceUpdated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Broadcast;
use Tests\TestCase;
use Tests\TestHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PriceUpdateTest extends TestCase
{
    use TestHelper;
    /**
     * Set up the test environment.
     *
     * This method is run before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test that price updates are broadcasted correctly.
     *
     * @return void
     */
    public function test_broadcasts_price_updates()
    {
        // Fake the event and listen for it
        Event::fake();

        $data = $this->generatePriceMockData();

        // Using spread operator to pass the array data
        event(new PriceUpdated(...$data)); 

        // Assert the event was broadcasted
        Event::assertDispatched(PriceUpdated::class);

        // You can also assert the event has the expected broadcast data
        $this->assertEquals($data['symbol'], $data['symbol']);
        $this->assertEquals($data['price'], $data['price']);
        $this->assertEquals($data['trend'], $data['trend']);
    }

    /**
     * Test that the WebSocket channel name is correct.
     *
     * @return void
     */
    public function test_websocket_channel_name_is_correct()
    {
        $data = $this->generatePriceMockData();

        $event = new PriceUpdated(...$data);

        // Assert that the correct channel is being used
        $this->assertEquals('prices', $event->broadcastOn()[0]->name);
    }

    /**
     * Test that the broadcasted data structure is correct.
     *
     * @return void
     */
    public function test_broadcast_data_structure()
    {
        $data = $this->generatePriceMockData();

        $event = new PriceUpdated(...$data);

        $broadcastData = $event->broadcastWith();

        // Assert that the broadcast data contains the correct structure
        $this->assertArrayHasKey('symbol', $broadcastData);
        $this->assertArrayHasKey('price', $broadcastData);
        $this->assertArrayHasKey('previous_price', $broadcastData);
        $this->assertArrayHasKey('exchanges', $broadcastData);
        $this->assertArrayHasKey('timestamp', $broadcastData);
        $this->assertArrayHasKey('trend', $broadcastData);
    }
}
