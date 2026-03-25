<?php

namespace Testa\Storefront\UseCases\Account;

use Testa\Models\Customer;
use Testa\Storefront\Data\AddressData;

final class CreateCustomerAddress
{
    public function execute(Customer $customer, AddressData $data): void
    {
        $customer->addresses()->create($data->toArray());
    }
}
