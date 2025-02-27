<?php

namespace Tests\Feature\Jobs;

use App\Jobs\PriceFetcherJob;
use App\Jobs\StorePriceUpdate;
use App\Services\FreeCryptoApiClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Tests\TestHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PriceFetcherJobFeatureTest extends TestCase
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

    // Test job handles an exception and retries the process as expected
    

    //Test job schedules next run after handling prices


    // Test job does not dispatch StorePriceUpdate when the API response is empty
    public function test_job_does_not_dispatch_when_no_prices_returned()
    {
        Queue::fake();

        $mockClient = $this->createMockClient([]);

        $job = new PriceFetcherJob(['BTC']);
        $job->handle($mockClient);

        // Assert that StorePriceUpdate is not dispatched when the response is empty
        Queue::assertNotPushed(StorePriceUpdate::class);
    }

    // Test job handles invalid symbols input gracefully
    public function test_job_handles_invalid_symbols_gracefully()
    {
        $this->expectException(\InvalidArgumentException::class);

        $symbols = null; // Invalid symbols
        new PriceFetcherJob([]);
    }

    // Test job handles and logs API response error (other than empty)
    public function test_job_logs_error_on_api_failure()
    {
        // Fake the queue so no actual jobs are pushed to the queue
        Queue::fake();

        // Spy on Log to verify the error logging
        Log::spy();

        // Mock client that will throw an exception
        $mockClient = $this->createMockClient([]);
        
        // Create the job instance
        $job = new PriceFetcherJob(['BTC']);
        
        // Simulate failure on the first attempt (throw exception)
        $mockClient->method('getPrices')->willThrowException(new \Exception('API Error'));
        
        // Handle the job, which should trigger the exception
        $job->handle($mockClient);

        // Assert that the error log method was called once with the correct arguments
        Log::shouldHaveReceived('error')->once()->withArgs(function ($message, $context) {
            return $message === 'Price fetcher error' && isset($context['error']) && $context['error'] === 'API Error';
        });
    }
}