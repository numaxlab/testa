<?php

namespace Testa\Database\Factories\News;

use Illuminate\Database\Eloquent\Factories\Factory;
use Testa\Models\News\EventType;

class EventTypeFactory extends Factory
{
    protected $model = EventType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
        ];
    }
}
