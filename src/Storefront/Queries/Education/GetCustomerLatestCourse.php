<?php

namespace Testa\Storefront\Queries\Education;

use Testa\Models\Customer;
use Testa\Models\Education\Course;

final class GetCustomerLatestCourse
{
    public function execute(Customer $customer): ?Course
    {
        return $customer
            ->courses()
            ->where('is_published', true)
            ->latest()
            ->first();
    }
}
