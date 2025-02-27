<?php

namespace App\Exceptions\Crypto;

class InvalidResponseException extends CryptoApiException
{
    public function __construct(string $exchange, string $reason)
    {
        parent::__construct($exchange, "Invalid response from {$exchange}: {$reason}", 400);
    }
} 