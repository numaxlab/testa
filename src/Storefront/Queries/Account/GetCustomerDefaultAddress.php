<?php

namespace Testa\Storefront\Queries\Account;

use Lunar\Models\Address;
use Testa\Models\Customer;

final class GetCustomerDefaultAddress
{
    public function execute(Customer $customer): ?Address
    {
        return $customer->addresses()->where('shipping_default', true)->first();
    }
}
