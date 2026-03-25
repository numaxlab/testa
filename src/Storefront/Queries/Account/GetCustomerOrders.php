<?php

namespace Testa\Storefront\Queries\Account;

use Illuminate\Pagination\LengthAwarePaginator;
use Testa\Models\Customer;

final class GetCustomerOrders
{
    public function execute(Customer $customer, int $perPage = 8): LengthAwarePaginator
    {
        return $customer
            ->orders()
            ->whereNotIn('status', ['awaiting-payment', 'cancelled'])
            ->where('is_geslib', true)
            ->latest()
            ->paginate($perPage);
    }
}
