<?php

namespace App\Exceptions\FreeCryptoApiClient;

class FreeCryptoApiInvalidResponseException extends FreeCryptoApiException
{
    public function __construct(string $exchange, string $message)
    {
        parent::__construct($exchange, "Invalid response from {$exchange}: {$message}", 400);
    }
}
