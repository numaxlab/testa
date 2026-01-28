<?php

namespace Testa\Database\Factories\Content;

use Illuminate\Database\Eloquent\Factories\Factory;
use Testa\Models\Content\Section;
use Testa\Models\Content\Tier;
use Testa\Models\Content\TierType;

class TierFactory extends Factory
{
    protected $model = Tier::class;

    public function definition(): array
    {
        return [
            'section' => Section::BOOKSHOP->value,
            'type' => TierType::ARTICLES_LATEST->value,
            'name' => $this->faker->words(6, true),
            'link' => $this->faker->url,
            'link_name' => $this->faker->name,
            'sort_position' => $this->faker->numberBetween(0, 10),
            'is_published' => true,
        ];
    }
}
