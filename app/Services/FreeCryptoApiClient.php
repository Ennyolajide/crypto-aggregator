<?php

namespace App\Services;

use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\ResultAggregator;
use App\Services\ResponseValidator;
use GuzzleHttp\Promise\PromiseInterface;
use App\Contracts\FreeCryptoApiClientInterface;
use App\Exceptions\FreeCryptoApiClient\FreeCryptoApiException;


/**
 * - SOLID (Single Responsibility): This service only handles API communication and response validation (getPrices(), fetchPriceWithRetry()).
 * - SOLID (Open/Closed): Implements PriceFetcherInterface, allowing new price fetchers to be added without modifying this class.
 * - SOLID (Dependency Inversion): High-level modules depend on the PriceFetcherInterface abstraction, not this concrete class.
 * - DRY: The fetchPriceWithRetry() method encapsulates retry logic, and buildRequests() is reused for all symbols.
 * - YAGNI: Only the required API endpoints and retry logic are implemented; no extra abstraction for unused endpoints or advanced error handling.
 */
class FreeCryptoApiClient implements FreeCryptoApiClientInterface
{
    private string $baseUrl;
    private string $apiKey;
    private array $exchanges;
    private int $maxRetries = 3;
    private ResponseValidator $validator;

    public function __construct()
    {
        $this->baseUrl = config('crypto.api_endpoint');
        $this->apiKey = config('crypto.api_key');
        $this->exchanges = config('crypto.exchanges');
        $this->validator = new ResponseValidator(); // Initialize validator
    }

    public function getPrices(array $symbols): array
    {
        $requests = array_merge(...array_map(fn ($symbol) => $this->buildRequests($symbol), $symbols));
        $promises = array_map(fn ($request) => $this->fetchPriceWithRetry($request), $requests);
        $responses = Utils::settle($promises)->wait();
        return (new ResultAggregator())->processResponses($responses, $requests);
    }

    private function buildRequests(string $symbol): array
    {
        return array_map(fn ($exchange) => [
            'symbol' => $symbol,
            'exchange' => $exchange,
            'query' => "{$symbol}@{$exchange}"
        ], $this->exchanges);
    }

    private function fetchPriceWithRetry(array $request, int $attempt = 0): PromiseInterface
    {
        return Http::async()->withHeaders([
            'Authorization' => "Bearer {$this->apiKey}"
        ])->get("{$this->baseUrl}/getData", ['symbol' => $request['query']])
        ->then(
            fn ($response) => $this->validator->validateResponse($response, $request), // Use validator
            function ($reason) use ($request, $attempt) {
                if ($attempt < $this->maxRetries) {
                    Log::warning("Retrying request for {$request['symbol']} at {$request['exchange']} - Attempt " . ($attempt + 1));
                    return $this->fetchPriceWithRetry($request, $attempt + 1);
                }
                Log::error("Request failed for {$request['symbol']} at {$request['exchange']}: " . $reason->getMessage());
                throw new FreeCryptoApiException($request['exchange'], "Failed to fetch price data for {$request['symbol']}", 500);
            }
        );
    }
}
