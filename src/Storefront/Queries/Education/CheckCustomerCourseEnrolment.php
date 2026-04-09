<?php

namespace Testa\Storefront\Queries\Education;

use Testa\Models\Customer;
use Testa\Models\Education\Course;

final class CheckCustomerCourseEnrolment
{
    public function execute(Customer $customer, Course $course): bool
    {
        return $customer->courses()->where('id', $course->id)->exists();
    }
}
