<?php

namespace App\Exceptions\Crypto;

class RateLimitException extends CryptoApiException
{
    public function __construct(string $exchange)
    {
        parent::__construct($exchange, "Rate limit exceeded for {$exchange}", 429);
    }
} 