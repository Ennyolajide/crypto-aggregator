<?php

namespace Tests\Unit\Services;

use App\Services\FreeCryptoApiClient;
use App\Contracts\FreeCryptoApiClientInterface;
use PHPUnit\Framework\TestCase;

class FreeCryptoApiClientTest extends TestCase
{
    public function test_implements_interface()
    {
        $client = new FreeCryptoApiClient();
        $this->assertInstanceOf(FreeCryptoApiClientInterface::class, $client);
    }
} 