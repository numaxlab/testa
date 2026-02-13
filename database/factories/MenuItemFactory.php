<?php

namespace Testa\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Testa\Models\MenuItem;

class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'type' => 'manual',
            'link_value' => $this->faker->url(),
            'sort_position' => 0,
            'is_published' => true,
        ];
    }

    public function manual(string $url = null): static
    {
        return $this->state(fn()
            => [
            'type' => 'manual',
            'link_value' => $url ?? $this->faker->url(),
        ]);
    }

    public function route(string $routeName): static
    {
        return $this->state(fn()
            => [
            'type' => 'route',
            'link_value' => $routeName,
        ]);
    }

    public function model(): static
    {
        return $this->state(fn()
            => [
            'type' => 'model',
            'link_value' => null,
        ]);
    }

    public function withParent(MenuItem $parent): static
    {
        return $this->state(fn()
            => [
            'parent_id' => $parent->id,
        ]);
    }

    public function group(): static
    {
        return $this->state(fn()
            => [
            'type' => 'group',
            'link_value' => null,
            'linkable_type' => null,
            'linkable_id' => null,
        ]);
    }

    public function unpublished(): static
    {
        return $this->state(fn()
            => [
            'is_published' => false,
        ]);
    }
}
