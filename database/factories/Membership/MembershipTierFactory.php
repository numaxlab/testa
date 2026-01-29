<?php

namespace Testa\Database\Factories\Membership;

use Illuminate\Database\Eloquent\Factories\Factory;
use Testa\Models\Membership\MembershipTier;

class MembershipTierFactory extends Factory
{
    protected $model = MembershipTier::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->paragraph(),
        ];
    }
}
