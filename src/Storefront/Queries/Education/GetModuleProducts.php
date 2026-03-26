<?php

namespace Testa\Storefront\Queries\Education;

use Illuminate\Support\Collection;
use Testa\Models\Education\CourseModule;
use Testa\Storefront\Queries\ProductQueryBuilder;

final class GetModuleProducts
{
    public function execute(CourseModule $module): Collection
    {
        $products = ProductQueryBuilder::fromRelation($module->products()->getQuery())->get();

        return $products->merge(
            ProductQueryBuilder::fromRelation($module->course->products()->getQuery())->get(),
        );
    }
}
