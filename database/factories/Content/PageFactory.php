<?php

namespace Testa\Database\Factories\Content;

use Illuminate\Database\Eloquent\Factories\Factory;
use Testa\Models\Content\Page;
use Testa\Models\Content\Section;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        return [
            'section' => Section::BOOKSHOP->value,
            'name' => $this->faker->words(6, true),
            'intro' => $this->faker->sentences(rand(3, 8), true),
            'description' => $this->faker->paragraph,
            'content' => [
                [
                    'name' => $this->faker->words(4),
                    'description' => $this->faker->paragraph,
                    'action' => $this->faker->url,
                    'action_tag' => $this->faker->word,
                ],
            ],
            'is_published' => true,
        ];
    }
}
