<?php

namespace Trafikrak\Database\Factories\Content;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trafikrak\Models\Content\Banner;
use Trafikrak\Models\Content\BannerType;

class BannerFactory extends Factory
{
    protected $model = Banner::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(rand(3, 8)),
            'type' => BannerType::CONTAINED->value,
            'description' => $this->faker->paragraph(rand(3, 8)),
            'link' => $this->faker->url(),
            'button_text' => $this->faker->word(),
            'image' => $this->faker->imageUrl(),
            'is_published' => true,
        ];
    }
}
