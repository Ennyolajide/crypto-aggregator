<?php

namespace App\Exceptions\FreeCryptoApiClient;

/**
 * - SOLID (Single Responsibility): This exception only represents rate limit errors for a specific exchange, making error handling clear and focused.
 * - DRY: The error message formatting is encapsulated, avoiding repetition in error handling.
 * - YAGNI: Only the required context (exchange) is included; no extra error metadata or abstraction.
 */
class FreeCryptoApiRateLimitException extends FreeCryptoApiException
{
    public function __construct(string $exchange)
    {
        parent::__construct($exchange, "Rate limit exceeded for {$exchange}", 429);
    }
}
