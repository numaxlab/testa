<?php

namespace Testa\Payment;

use Testa\Models\Membership\Subscription;

final readonly class RedsysRecurringChargeData
{
    public function __construct(
        public Subscription $subscription,
        public string $paymentIdentifier,
        public string $configKey,
        public int $amount,
    ) {}
}
