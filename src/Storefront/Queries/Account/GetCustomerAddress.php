<?php

namespace Testa\Storefront\Queries\Account;

use Lunar\Models\Address;
use Testa\Models\Customer;

final class GetCustomerAddress
{
    public function execute(Customer $customer, int $id): Address
    {
        return $customer->addresses()->findOrFail($id);
    }
}
