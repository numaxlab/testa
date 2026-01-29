<?php

namespace Testa\Database\Factories\Education;

use Illuminate\Database\Eloquent\Factories\Factory;
use Testa\Models\Education\Course;
use Testa\Models\Education\CourseModule;
use Testa\Models\EventDeliveryMethod;

class CourseModuleFactory extends Factory
{
    protected $model = CourseModule::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'name' => $this->faker->sentence(rand(3, 6)),
            'subtitle' => $this->faker->sentence(rand(3, 8)),
            'description' => $this->faker->paragraph(rand(3, 8)),
            'starts_at' => now(),
            'delivery_method' => EventDeliveryMethod::IN_PERSON->value,
            'is_published' => true,
        ];
    }

    public function inPerson(): static
    {
        return $this->state([
            'delivery_method' => EventDeliveryMethod::IN_PERSON->value,
        ]);
    }

    public function online(): static
    {
        return $this->state([
            'delivery_method' => EventDeliveryMethod::ONLINE->value,
        ]);
    }

    public function hybrid(): static
    {
        return $this->state([
            'delivery_method' => EventDeliveryMethod::HYBRID->value,
        ]);
    }
}
