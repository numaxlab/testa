<?php

namespace Testa\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Testa\Models\Venue;

class VenueFactory extends Factory
{
    protected $model = Venue::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
        ];
    }
}
