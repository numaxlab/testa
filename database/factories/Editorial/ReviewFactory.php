<?php

namespace Testa\Database\Factories\Editorial;

use Illuminate\Database\Eloquent\Factories\Factory;
use Lunar\Models\Product;
use Testa\Models\Editorial\Review;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'quote' => $this->faker->paragraph(),
            'author' => $this->faker->name(),
            'media_name' => $this->faker->company(),
            'link' => $this->faker->url(),
        ];
    }
}
