<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FreeCryptoApiClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FreeCryptoApiClient::class, function ($app) {
            return new FreeCryptoApiClient();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
