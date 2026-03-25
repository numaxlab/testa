<?php

namespace Testa\Storefront\UseCases\Account;

use Lunar\Models\Address;
use Testa\Storefront\Data\AddressData;

final class UpdateAddress
{
    public function execute(Address $address, AddressData $data): void
    {
        $address->update($data->toArray());
    }
}
