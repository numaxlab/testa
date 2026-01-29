<?php

namespace Testa\Database\Factories\Education;

use Illuminate\Database\Eloquent\Factories\Factory;
use Testa\Models\Education\Price;

class PriceFactory extends Factory
{
    protected $model = Price::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'amount' => $this->faker->numberBetween(10, 500) * 100,
        ];
    }
}
