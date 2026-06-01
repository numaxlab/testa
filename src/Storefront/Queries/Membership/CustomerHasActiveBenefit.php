<?php

namespace Testa\Storefront\Queries\Membership;

use Testa\Models\Customer;

final class CustomerHasActiveBenefit
{
    public function execute(Customer $customer, string $benefitCode): bool
    {
        return $customer->activeSubscriptions()
            ->whereHas('plan.benefits', fn ($q) => $q->where('code', $benefitCode))
            ->exists();
    }
}
