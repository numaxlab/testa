<?php

namespace Testa\Database\Factories\News;

use Illuminate\Database\Eloquent\Factories\Factory;
use Testa\Models\News\Article;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(rand(3, 8)),
            'summary' => $this->faker->paragraph(),
            'content' => $this->faker->paragraphs(5, true),
            'published_at' => now(),
            'is_published' => true,
        ];
    }

    public function unpublished(): static
    {
        return $this->state([
            'is_published' => false,
        ]);
    }
}
