<?php

namespace Testa\Storefront\Queries\Account;

use Illuminate\Database\Eloquent\Collection;
use Testa\Models\Customer;

final class GetCustomerActiveSubscriptions
{
    public function execute(Customer $customer): Collection
    {
        return $customer->activeSubscriptions;
    }
}
