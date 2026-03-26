<?php

namespace Testa\Storefront\Queries\Account;

use Illuminate\Database\Eloquent\Collection;
use Testa\Models\Customer;

final class GetCustomerLatestOrders
{
    public function execute(Customer $customer, int $limit = 2): Collection
    {
        return $customer
            ->orders()
            ->where('is_geslib', true)
            ->whereNotIn('status', ['awaiting-payment', 'cancelled'])
            ->latest()
            ->take($limit)
            ->get();
    }
}
