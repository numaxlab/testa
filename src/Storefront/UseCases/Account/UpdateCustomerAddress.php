<?php

namespace Testa\Storefront\UseCases\Account;

use Illuminate\Auth\Access\AuthorizationException;
use Lunar\Models\Address;
use Testa\Models\Customer;
use Testa\Storefront\Data\AddressData;

final class UpdateCustomerAddress
{
    public function execute(Customer $customer, Address $address, AddressData $data): void
    {
        if ($address->customer_id !== $customer->id) {
            throw new AuthorizationException();
        }

        $address->update($data->toArray());
    }
}
