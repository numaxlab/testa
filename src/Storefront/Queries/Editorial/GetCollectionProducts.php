<?php

namespace Testa\Storefront\Queries\Editorial;

use Illuminate\Pagination\LengthAwarePaginator;
use Lunar\Models\Collection;
use Testa\Storefront\Queries\ProductQueryBuilder;

final class GetCollectionProducts
{
    public function execute(Collection $collection, int $perPage = 18): LengthAwarePaginator
    {
        return ProductQueryBuilder::build()
            ->whereHas('collections', function ($query) use ($collection) {
                $query->where((new Collection)->getTable().'.id', $collection->id);
            })
            ->paginate($perPage);
    }
}
