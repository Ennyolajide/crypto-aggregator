<?php

namespace Tests;

use ReflectionClass;
use Illuminate\Foundation\Testing\WithFaker;

trait TestHelper
{
    use WithFaker;
    /**
     * Helper function to get private properties.
     *
     * @param object $object
     * @param string $property
     * @return mixed
     */
    protected function getPrivateProperty($object, string $property)
    {
        $reflection = new ReflectionClass($object);
        $prop = $reflection->getProperty($property);
        $prop->setAccessible(true);  // Make sure the property is accessible.
        return $prop->getValue($object);
    }

    /**
     * Helper function to call private methods.
     *
     * @param object $object
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    protected function callPrivateMethod($object, string $method, array $parameters)
    {
        $reflection = new ReflectionClass($object);
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }


    /**
     * Helper function to generate random data for price update
     *
     * @return array
     */private function generatePriceMockData(String $symbol = ''): array
       {
        return [
            'symbol' => $symbol ?? $this->faker->word, // Random symbol
            'price' => $this->faker->randomFloat(2, 900, 1200), // Random price (between 900 and 1200)
            'lastBtc' => $this->faker->randomFloat(2, 0, 1), // Random last BTC price
            'lowest' => $this->faker->randomFloat(2, 500, 800), // Random lowest price
            'highest' => $this->faker->randomFloat(2, 1300, 2000), // Random highest price
            'dailyChange' => $this->faker->randomFloat(2, -10, 10), // Random daily change percentage
            'date' => $this->faker->date('Y-m-d'), // Random date
            'exchanges' => $this->faker->words(3), // Random list of exchange names
        ];
    }

}
