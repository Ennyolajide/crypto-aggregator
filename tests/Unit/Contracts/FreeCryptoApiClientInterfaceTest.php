<?php

namespace Tests\Unit\Contracts;

use App\Contracts\FreeCryptoApiClientInterface;
use PHPUnit\Framework\TestCase;

class DummyCryptoApiClient implements FreeCryptoApiClientInterface
{
    public function getPrices(array $symbols): array
    {
        return array_map(fn($symbol) => ['symbol' => $symbol, 'price' => 1.23], $symbols);
    }
}

class FreeCryptoApiClientInterfaceTest extends TestCase
{
    public function test_lsp_compliance()
    {
        $client = new DummyCryptoApiClient();
        $result = $client->getPrices(['BTCUSDT']);
        $this->assertEquals('BTCUSDT', $result[0]['symbol']);
        $this->assertEquals(1.23, $result[0]['price']);
    }
} 