<?php

namespace Testa\Storefront\Queries\Account;

use Illuminate\Database\Eloquent\Collection;
use Testa\Models\Customer;

final class GetCustomerAddresses
{
    public function execute(Customer $customer): Collection
    {
        return $customer->addresses()->get();
    }
}
