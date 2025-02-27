<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Jobs\StorePriceUpdate;
use Tests\TestHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StorePriceUpdateUnitTest extends TestCase
{
    use TestHelper, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test calculating neutral trend when current price equals previous price.
     *
     * @return void
     */
    public function test_calculate_trend_neutral()
    {
        $data = $this->generatePriceMockData();

        // Set previous price to be equal to the current price for a neutral trend
        $previousPrice = $data['price'];

        // Create the job instance using the spread operator
        $job = new StorePriceUpdate(
            ...$data // Spread the data array to fill the constructor
        );

        // Use callPrivateMethod to call the private calculateTrend method
        $trend = $this->callPrivateMethod($job, 'calculateTrend', [$data['price'], $previousPrice]);

        // Assert that the trend is neutral
        $this->assertEquals('neutral', $trend);
    }

    /**
     * Test calculating up trend when current price is higher than previous price.
     *
     * @return void
     */
    public function test_calculate_trend_up()
    {
        $data = $this->generatePriceMockData();

        // Set previous price to be lower than the current price for an up trend
        $previousPrice = $data['price'] - 1;

        // Create the job instance using the spread operator
        $job = new StorePriceUpdate(
            ...$data // Spread the data array to fill the constructor
        );

        // Use callPrivateMethod to call the private calculateTrend method
        $trend = $this->callPrivateMethod($job, 'calculateTrend', [$data['price'], $previousPrice]);

        // Assert that the trend is up
        $this->assertEquals('up', $trend);
    }

    /**
     * Test calculating down trend when current price is lower than previous price.
     *
     * @return void
     */
    public function test_calculate_trend_down()
    {
        $data = $this->generatePriceMockData();

        // Set previous price to be higher than the current price for a down trend
        $previousPrice = $data['price'] + 1;

        // Create the job instance using the spread operator
        $job = new StorePriceUpdate(
            ...$data // Spread the data array to fill the constructor
        );

        // Use callPrivateMethod to call the private calculateTrend method
        $trend = $this->callPrivateMethod($job, 'calculateTrend', [$data['price'], $previousPrice]);

        // Assert that the trend is down
        $this->assertEquals('down', $trend);
    }
}
