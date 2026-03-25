<?php

namespace Testa\Storefront\Queries\Account;

use Lunar\Models\Order;
use Testa\Models\Customer;

final class GetCustomerOrder
{
    public function execute(Customer $customer, string $reference): Order
    {
        return $customer
            ->orders()
            ->where('reference', $reference)
            ->whereNotIn('status', ['awaiting-payment', 'cancelled'])
            ->firstOrFail();
    }
}
