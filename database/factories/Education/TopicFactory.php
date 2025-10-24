<?php

namespace Trafikrak\Database\Factories\Education;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trafikrak\Models\Education\Topic;

class TopicFactory extends Factory
{
    protected $model = Topic::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence,
            'subtitle' => $this->faker->sentence(rand(3, 8)),
            'description' => $this->faker->paragraph(rand(3, 8)),
            'is_published' => true,
        ];
    }
}
