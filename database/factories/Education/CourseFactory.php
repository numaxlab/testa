<?php

namespace Testa\Database\Factories\Education;

use Illuminate\Database\Eloquent\Factories\Factory;
use Testa\Models\Education\Course;
use Testa\Models\Education\Topic;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(rand(3, 8)),
            'subtitle' => $this->faker->sentence(rand(3, 8)),
            'description' => $this->faker->paragraph(rand(3, 8)),
            'starts_at' => now(),
            'ends_at' => now()->addDays(7),
            'topic_id' => Topic::factory(),
            'is_published' => true,
        ];
    }
}
