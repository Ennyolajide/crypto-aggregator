<?php

namespace Tests\Unit\Providers;

use App\Contracts\FreeCryptoApiClientInterface;
use App\Services\FreeCryptoApiClient;
use Illuminate\Foundation\Application;
use Tests\TestCase;

class AppServiceProviderTest extends TestCase
{
    public function test_interface_binding()
    {
        $client = $this->app->make(FreeCryptoApiClientInterface::class);
        $this->assertInstanceOf(FreeCryptoApiClient::class, $client);
    }
} 