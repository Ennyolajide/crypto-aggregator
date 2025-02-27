<?php

namespace Database\Factories;

use App\Models\CryptoPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

class CryptoPriceFactory extends Factory
{
    protected $model = CryptoPrice::class;

    public function definition(): array
    {
        $price = $this->faker->randomFloat(2, 20000, 60000);
        $previousPrice = $price * (1 + $this->faker->randomFloat(4, -0.05, 0.05));
        $lastBtc = $price / $this->faker->randomFloat(2, 20000, 60000);
        
        return [
            'symbol' => $this->faker->randomElement(['BTC', 'ETH', 'ETHBTC']),
            'price' => $price,
            'previous_price' => $previousPrice,
            'last_btc' => $lastBtc,
            'lowest' => $price * 0.95,
            'highest' => $price * 1.05,
            'daily_change_percentage' => (($price - $previousPrice) / $previousPrice) * 100,
            'exchanges' => $this->faker->randomElements(
                ['binance', 'mexc', 'huobi'],
                $this->faker->numberBetween(1, 3)
            ),
            //'timestamp' => now()->subSeconds($this->faker->numberBetween(0, 300))
        ];
    }

    /**
     * Indicate that the price is trending up
     */
    public function trendingUp(): Factory
    {
        return $this->state(function (array $attributes) {
            $price = $attributes['price'] ?? $this->faker->randomFloat(2, 20000, 60000);
            return [
                'previous_price' => $price * 0.95,
                'daily_change_percentage' => 5.0
            ];
        });
    }

    /**
     * Indicate that the price is trending down
     */
    public function trendingDown(): Factory
    {
        return $this->state(function (array $attributes) {
            $price = $attributes['price'] ?? $this->faker->randomFloat(2, 20000, 60000);
            return [
                'previous_price' => $price * 1.05,
                'daily_change_percentage' => -5.0
            ];
        });
    }

    /**
     * Configure the model factory.
     */
    public function configure()
    {
        return $this->afterMaking(function (CryptoPrice $price) {
            //
        })->afterCreating(function (CryptoPrice $price) {
            //
        });
    }

}