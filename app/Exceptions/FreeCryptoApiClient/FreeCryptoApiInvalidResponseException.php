<?php

namespace App\Exceptions\FreeCryptoApiClient;

/**
 * - SOLID (Single Responsibility): This exception only represents invalid response errors for a specific exchange, making error handling clear and focused.
 * - DRY: The error message formatting is encapsulated, avoiding repetition in error handling.
 * - YAGNI: Only the required context (exchange and message) is included; no extra error metadata or abstraction.
 */
class FreeCryptoApiInvalidResponseException extends FreeCryptoApiException
{
    public function __construct(string $exchange, string $message)
    {
        parent::__construct($exchange, "Invalid response from {$exchange}: {$message}", 400);
    }
}
