<?php

namespace Tests\Unit\Jobs;

use App\Jobs\PriceFetcherJob;
use App\Jobs\StorePriceUpdate;
use App\Services\FreeCryptoApiClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Tests\TestHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PriceFetcherJobUnitTest extends TestCase
{
    use TestHelper, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    // Helper method to create mock data for the API response
    private function createMockPricesData($symbols)
    {
        return array_map(function ($symbol) {
            return $this->generatePriceMockData($symbol);
        }, $symbols);
    }

    // Helper method to create a mock API client
    private function createMockClient($mockData)
    {
        $mockClient = $this->createMock(FreeCryptoApiClient::class);
        $mockClient->method('getPrices')->willReturn($mockData);
        return $mockClient;
    }

    //Test job processes prices correctly for multiple symbols
    // public function test_job_processes_prices_correctly_for_single_or_multiple_symbols()
    // {
    //     Queue::fake();

    //     // Define the available symbols
    //     $symbols = ['BTC', 'ETH'];

    //     $mockData = $this->createMockPricesData($symbols);

    //     $mockClient = $this->createMockClient($mockData);

    //     Log::spy(); // Spy on Log to verify later

    //     // Create the job with the array of symbols
    //     $job = new PriceFetcherJob($symbols);
    //     $job->handle($mockClient);

    //     // Assert that the StorePriceUpdate job was pushed with the correct data for each symbol
    //     foreach ($symbols as $index => $symbol) {
    //         Queue::assertPushed(StorePriceUpdate::class, function ($job) use ($mockData, $symbol, $index) {
    //             return
    //                 $this->getPrivateProperty($job, 'symbol') == $symbol &&
    //                 $this->getPrivateProperty($job, 'price') == $mockData[$index]['price'] &&
    //                 $this->getPrivateProperty($job, 'lastBtc') == $mockData[$index]['last_btc'] &&
    //                 $this->getPrivateProperty($job, 'lowest') == $mockData[$index]['lowest'] &&
    //                 $this->getPrivateProperty($job, 'highest') == $mockData[$index]['highest'] &&
    //                 $this->getPrivateProperty($job, 'date') == $mockData[$index]['timestamp'] &&
    //                 $this->getPrivateProperty($job, 'exchanges') == $mockData[$index]['exchanges'] &&
    //                 $this->getPrivateProperty($job, 'dailyChange') == $mockData[$index]['daily_change_percentage'];
    //         });

    //         Log::shouldHaveReceived('info')->withArgs(function ($message, $context) use ($symbol) {
    //             return $message === 'Dispatched price update' && $context['symbol'] === $symbol;
    //         });
    //     }
    // }

    public function test_job_handles_empty_response_and_retries()
    {
        Queue::fake();

        $mockClient = $this->createMockClient([]);

        Log::spy();

        $job = new PriceFetcherJob(['BTC']);
        $job->handle($mockClient);

        Queue::assertPushed(PriceFetcherJob::class);
        Log::shouldHaveReceived('warning')->once();
    }

    public function test_job_fails_after_max_attempts()
    {
        Queue::fake();

        $mockClient = $this->createMockClient([]);

        Log::spy();

        $job = new PriceFetcherJob(['BTC']);

        // Simulate failure multiple times
        for ($i = 1; $i <= 3; $i++) {
            $mockClient->method('getPrices')->willThrowException(new \Exception('Critical API failure'));
            $job->handle($mockClient);
        }

        Queue::assertNotPushed(PriceFetcherJob::class);
        Log::shouldHaveReceived('error')->times(3);
    }

    public function test_constructor_throws_exception_for_empty_symbols()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PriceFetcherJob([]);
    }
}
