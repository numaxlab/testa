<?php

namespace Testa\Database\Factories\Membership;

use Illuminate\Database\Eloquent\Factories\Factory;
use Testa\Models\Membership\MembershipPlan;
use Testa\Models\Membership\MembershipTier;

class MembershipPlanFactory extends Factory
{
    protected $model = MembershipPlan::class;

    public function definition(): array
    {
        return [
            'membership_tier_id' => MembershipTier::factory(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'billing_interval' => MembershipPlan::BILLING_INTERVAL_YEARLY,
        ];
    }

    public function monthly(): static
    {
        return $this->state([
            'billing_interval' => MembershipPlan::BILLING_INTERVAL_MONTHLY,
        ]);
    }

    public function bimonthly(): static
    {
        return $this->state([
            'billing_interval' => MembershipPlan::BILLING_INTERVAL_BIMONTHLY,
        ]);
    }

    public function quarterly(): static
    {
        return $this->state([
            'billing_interval' => MembershipPlan::BILLING_INTERVAL_QUARTERLY,
        ]);
    }

    public function yearly(): static
    {
        return $this->state([
            'billing_interval' => MembershipPlan::BILLING_INTERVAL_YEARLY,
        ]);
    }
}
