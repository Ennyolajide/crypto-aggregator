<?php

namespace App\Contracts;

/**
 * - SOLID (Open/Closed): Enables new price fetcher implementations to be added without modifying existing code.
 * - SOLID (Dependency Inversion): Allows high-level modules to depend on this abstraction rather than concrete classes.
 * - SOLID (Interface Segregation): Provides a focused contract for price fetching, so classes only implement what they need.
 * - SOLID (Liskov Substitution): Any implementation of this interface can be substituted without breaking the system, as long as it adheres to the contract.
 */
interface FreeCryptoApiClientInterface {
    /**
     * Fetch prices for the given symbols.
     *
     * @param array $symbols
     * @return array
     */
    public function getPrices(array $symbols): array;
}
