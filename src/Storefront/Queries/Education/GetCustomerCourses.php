<?php

namespace Testa\Storefront\Queries\Education;

use Illuminate\Pagination\LengthAwarePaginator;
use Testa\Models\Customer;

final class GetCustomerCourses
{
    public function execute(Customer $customer, int $perPage = 6): LengthAwarePaginator
    {
        return $customer
            ->courses()
            ->where('is_published', true)
            ->with(['horizontalImage', 'verticalImage'])
            ->paginate($perPage);
    }
}
