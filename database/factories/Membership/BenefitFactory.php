<?php

namespace Testa\Database\Factories\Membership;

use Illuminate\Database\Eloquent\Factories\Factory;
use Testa\Models\Membership\Benefit;

class BenefitFactory extends Factory
{
    protected $model = Benefit::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'code' => $this->faker->unique()->slug(2),
        ];
    }

    public function creditPaymentType(): static
    {
        return $this->state([
            'code' => Benefit::CREDIT_PAYMENT_TYPE,
        ]);
    }

    public function customerGroup(): static
    {
        return $this->state([
            'code' => Benefit::CUSTOMER_GROUP,
        ]);
    }
}
