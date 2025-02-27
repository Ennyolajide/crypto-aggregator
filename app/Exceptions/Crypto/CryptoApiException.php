<?php

namespace App\Exceptions\Crypto;

use Exception;

class CryptoApiException extends Exception
{
    protected string $exchange;

    public function __construct(string $exchange, string $message, int $code = 0)
    {
        $this->exchange = $exchange;
        parent::__construct($message, $code);
    }

    public function getExchange(): string
    {
        return $this->exchange;
    }
} 