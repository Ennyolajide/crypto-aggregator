<?php

namespace App\Exceptions;

use Exception;

/**
 * - SOLID (Single Responsibility): This exception only represents errors related to a specific price pair, keeping error handling focused and meaningful.
 * - DRY: The pair logic and message formatting are encapsulated in this class, avoiding repetition in error handling.
 * - YAGNI: Only the required context (pair and message) is included; no extra error metadata or abstraction.
 */
class PriceException extends Exception
{
    protected $pair;

    public function __construct(string $pair, string $message)
    {
        $this->pair = $pair;
        parent::__construct("[{$pair}] {$message}");
    }

    public function getPair(): string
    {
        return $this->pair;
    }
}