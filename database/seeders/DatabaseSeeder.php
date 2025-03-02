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

        $initialPrices = [
            'BTCUSDT' => [
                'price' => 85665.423333333,
                'previous_price' => 85656.023333333,
                'last_btc' => 0,
                'lowest' => 85311.51,
                'highest' => 107186.38,
                'daily_change_percentage' => -0.41096332015426,
                'exchanges' => ['binance', 'mexc', 'huobi'],
                'trend' => 'up'
            ],
            'ETHUSDT' => [
                'price' => 2214.36,
                'previous_price' => 2213.9566666667,
                'last_btc' => 0,
                'lowest' => 2194.47,
                'highest' => 2256.58,
                'daily_change_percentage' => -0.073106524346708,
                'exchanges' => ['binance', 'huobi'],
                'trend' => 'up'
            ],
            'BNBUSDT' => [
                'price' => 606.38333333333,
                'previous_price' => 606.56,
                'last_btc' => 0,
                'lowest' => 604.24,
                'highest' => 611.44,
                'daily_change_percentage' => -0.19586137086751,
                'exchanges' => ['binance', 'mexc', 'huobi'],
                'trend' => 'down'
            ],
            'ETHBTC' => [
                'price' => 0.0258385,
                'previous_price' => 0.025853,
                'last_btc' => 0,
                'lowest' => 0.025572,
                'highest' => 0.02626,
                'daily_change_percentage' => 0.30671307852025,
                'exchanges' => ['binance', 'huobi'],
                'trend' => 'down'
            ]
        ];

        foreach ($initialPrices as $symbol => $data) {
            CryptoPrice::create([
                'symbol' => $symbol,
                'price' => $data['price'],
                'previous_price' => $data['previous_price'],
                'last_btc' => $data['last_btc'],
                'lowest' => $data['lowest'],
                'highest' => $data['highest'],
                'daily_change_percentage' => $data['daily_change_percentage'],
                'exchanges' => $data['exchanges'],
                'trend' => $data['trend']
            ]);
        }

       
    }
}
