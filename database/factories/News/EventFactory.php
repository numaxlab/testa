<?php

namespace Testa\Database\Factories\News;

use Illuminate\Database\Eloquent\Factories\Factory;
use Testa\Models\EventDeliveryMethod;
use Testa\Models\News\Event;
use Testa\Models\News\EventType;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'event_type_id' => EventType::factory(),
            'name' => $this->faker->sentence(rand(3, 6)),
            'subtitle' => $this->faker->sentence(rand(3, 8)),
            'description' => $this->faker->paragraph(rand(3, 8)),
            'starts_at' => now()->addDays(7),
            'delivery_method' => EventDeliveryMethod::IN_PERSON->value,
            'image' => $this->faker->imageUrl(),
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
