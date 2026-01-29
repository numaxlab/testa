<?php

namespace Testa\Database\Factories\Content;

use Illuminate\Database\Eloquent\Factories\Factory;
use Testa\Models\Content\Section;
use Testa\Models\Content\Slide;

class SlideFactory extends Factory
{
    protected $model = Slide::class;

    public function definition(): array
    {
        return [
            'section' => Section::HOMEPAGE->value,
            'name' => $this->faker->sentence(rand(3, 6)),
            'description' => $this->faker->paragraph(),
            'link' => $this->faker->url(),
            'button_text' => $this->faker->words(2, true),
            'is_published' => true,
        ];
    }

    public function homepage(): static
    {
        return $this->state(['section' => Section::HOMEPAGE->value]);
    }

    public function bookshop(): static
    {
        return $this->state(['section' => Section::BOOKSHOP->value]);
    }
}
