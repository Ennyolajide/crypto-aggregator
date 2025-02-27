<?php

namespace App\Exceptions\Crypto;

class ConnectionException extends CryptoApiException
{
    public function __construct(string $exchange, string $reason)
    {
        parent::__construct($exchange, "Connection error with {$exchange}: {$reason}", 503);
    }
} 