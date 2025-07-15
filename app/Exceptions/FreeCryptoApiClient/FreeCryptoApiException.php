<?php

namespace App\Exceptions\FreeCryptoApiClient;

use Exception;

/**
 * - SOLID (Single Responsibility): This exception only represents errors related to a specific exchange, keeping error handling focused and meaningful.
 * - DRY: The exchange logic and message formatting are encapsulated in this class, avoiding repetition in error handling.
 * - YAGNI: Only the required context (exchange and message) is included; no extra error metadata or abstraction.
 */
class FreeCryptoApiException extends Exception
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
