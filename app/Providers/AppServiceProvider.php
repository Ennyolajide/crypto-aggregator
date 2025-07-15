<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FreeCryptoApiClient;
use App\Contracts\FreeCryptoApiClientInterface;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FreeCryptoApiClientInterface::class, FreeCryptoApiClient::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
