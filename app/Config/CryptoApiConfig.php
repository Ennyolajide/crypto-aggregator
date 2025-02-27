<?php

namespace App\Config;

class CryptoApiConfig
{
    public function __construct(
        public readonly string $baseUrl,
        public readonly string $apiKey,
        public readonly int $timeout,
        public readonly int $retries,
        public readonly int $retryDelay,
        public readonly int $cacheTtl,
        public readonly array $exchanges
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            baseUrl: config('crypto.api_endpoint', 'https://api.freecryptoapi.com/v1'),
            apiKey: config('crypto.api_key'),
            timeout: config('crypto.api.timeout', 5),
            retries: config('crypto.api.retries', 3),
            retryDelay: config('crypto.api.retry_delay', 100),
            cacheTtl: config('crypto.cache_ttl', 5),
            exchanges: config('crypto.exchanges', ['binance', 'mexc', 'huobi'])
        );
    }
} 