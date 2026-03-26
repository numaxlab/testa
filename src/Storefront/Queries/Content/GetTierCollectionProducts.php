<?php

namespace Testa\Storefront\Queries\Content;

use Illuminate\Database\Eloquent\Collection;
use Lunar\Models\Collection as LunarCollection;
use Testa\Models\Content\Tier;
use Testa\Storefront\Queries\ProductQueryBuilder;

final class GetTierCollectionProducts
{
    private const int LIMIT = 12;

    public function execute(Tier $tier): Collection
    {
        return ProductQueryBuilder::build()
            ->whereHas('collections', function ($query) use ($tier) {
                $query->whereIn(
                    (new LunarCollection)->getTable().'.id',
                    $tier->collections->pluck('id')->toArray(),
                );
            })
            ->take(self::LIMIT)
            ->get();
    }
}
