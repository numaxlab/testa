<?php

namespace Testa\Database\Factories\Membership;

use Illuminate\Database\Eloquent\Factories\Factory;
use Lunar\Models\Customer;
use Lunar\Models\Order;
use Testa\Models\Membership\MembershipPlan;
use Testa\Models\Membership\Subscription;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'membership_plan_id' => MembershipPlan::factory(),
            'order_id' => Order::factory(),
            'status' => Subscription::STATUS_ACTIVE,
            'started_at' => now(),
            'expires_at' => now()->addYear(),
        ];
    }

    public function active(): static
    {
        return $this->state([
            'status' => Subscription::STATUS_ACTIVE,
            'expires_at' => now()->addYear(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state([
            'status' => Subscription::STATUS_CANCELLED,
        ]);
    }

    public function expired(): static
    {
        return $this->state([
            'expires_at' => now()->subDay(),
        ]);
    }
}
