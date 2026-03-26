<?php

namespace Testa\Storefront\Queries\Bookshop;

use Illuminate\Database\Eloquent\Collection;
use Lunar\Models\Collection as LunarCollection;
use Testa\Storefront\Queries\ProductQueryBuilder;

final class GetTaxonomyProducts
{
    public function execute(LunarCollection $collection, int $limit = 6): Collection
    {
        return ProductQueryBuilder::build()
            ->whereHas('collections', function ($query) use ($collection) {
                $query->whereIn(
                    (new LunarCollection)->getTable().'.id',
                    array_merge([$collection->id], $collection->descendants->pluck('id')->toArray()),
                );
            })
            ->take($limit)
            ->get();
    }
}
