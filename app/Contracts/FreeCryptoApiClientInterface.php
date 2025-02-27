<?php

namespace App\Contracts;

use GuzzleHttp\Promise\PromiseInterface;

interface FreeCryptoApiClientInterface
{
    public function getPrices(array $symbols): array;

    public function getSupportedPairs(): array;

    public function fetchPriceWithRetry(array $request, int $attempt = 0): PromiseInterface;
}
