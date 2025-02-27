<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CryptoPrice;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Create some initial price data
        foreach (['BTC', 'ETH', 'ETHBTC'] as $symbol) {
            CryptoPrice::create([
                'symbol' => $symbol,
                'price' => match($symbol) {
                    'BTC' => 50000.00,
                    'ETH' => 3000.00,
                    'ETHBTC' => 0.06
                },
                'previous_price' => match($symbol) {
                    'BTC' => 49000.00,
                    'ETH' => 2900.00,
                    'ETHBTC' => 0.059
                },
                'last_btc' => match($symbol) {
                    'BTC' => 1.0,
                    'ETH' => 0.06,
                    'ETHBTC' => 0.06
                },
                'lowest' => match($symbol) {
                    'BTC' => 48000.00,
                    'ETH' => 2800.00,
                    'ETHBTC' => 0.058
                },
                'highest' => match($symbol) {
                    'BTC' => 51000.00,
                    'ETH' => 3100.00,
                    'ETHBTC' => 0.061
                },
                'daily_change_percentage' => 2.0,
                'exchanges' => ['binance', 'mexc', 'huobi'],
                'created_at' => now()
            ]);
        }

        // Create some random historical data
        if (app()->environment('local', 'development')) {
            CryptoPrice::factory()
                ->count(50)
                ->sequence(
                    ['symbol' => 'BTC'],
                    ['symbol' => 'ETH'],
                    ['symbol' => 'ETHBTC']
                )
                ->create();
        }
    }
}
