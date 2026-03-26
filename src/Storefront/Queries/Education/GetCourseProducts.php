<?php

namespace Testa\Storefront\Queries\Education;

use Illuminate\Support\Collection;
use Testa\Models\Education\Course;
use Testa\Storefront\Queries\ProductQueryBuilder;

final class GetCourseProducts
{
    public function execute(Course $course): Collection
    {
        $products = ProductQueryBuilder::fromRelation($course->products()->getQuery())->get();

        $course->modules->each(function ($module) use (&$products) {
            $products = $products->merge(
                ProductQueryBuilder::fromRelation($module->products()->getQuery())->get(),
            );
        });

        return $products;
    }
}
