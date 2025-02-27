<?php

namespace App\Exceptions\FreeCryptoApiClient;

class FreeCryptoApiRateLimitException extends FreeCryptoApiException
{
    public function __construct(string $exchange)
    {
        parent::__construct($exchange, "Rate limit exceeded for {$exchange}", 429);
    }
}
