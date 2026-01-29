<?php

namespace Testa\Database\Factories\Media;

use Illuminate\Database\Eloquent\Factories\Factory;
use Testa\Models\Media\Audio;
use Testa\Models\Media\Visibility;

class AudioFactory extends Factory
{
    protected $model = Audio::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'visibility' => Visibility::PUBLIC->value,
            'source' => 'ivoox',
            'source_id' => $this->faker->uuid(),
            'is_published' => true,
        ];
    }

    public function public(): static
    {
        return $this->state([
            'visibility' => Visibility::PUBLIC->value,
        ]);
    }

    public function private(): static
    {
        return $this->state([
            'visibility' => Visibility::PRIVATE->value,
        ]);
    }
}
