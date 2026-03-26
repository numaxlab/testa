<?php

namespace Testa\Storefront\UseCases\Account;

use Illuminate\Auth\Access\AuthorizationException;
use Lunar\Models\Address;
use Testa\Models\Customer;

final class DeleteCustomerAddress
{
    public function execute(Customer $customer, Address $address): void
    {
        if ($address->customer_id !== $customer->id) {
            throw new AuthorizationException();
        }

        $address->delete();
    }
}
