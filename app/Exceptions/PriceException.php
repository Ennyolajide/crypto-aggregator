<?php

namespace App\Exceptions;

use Exception;

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