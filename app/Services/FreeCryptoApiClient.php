<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Promise\PromiseInterface;
use App\Exceptions\FreeCryptoApiClient\FreeCryptoApiException;
use App\Services\ResultAggregator;
use App\Services\ResponseValidator; // Import the new class

class FreeCryptoApiClient
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
