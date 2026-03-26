<?php

namespace Testa\Storefront\Queries\Bookshop;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Collection as LunarCollection;
use NumaxLab\Lunar\Geslib\Handle;

final class SearchTaxonomies
{
    public function execute(string $search): Collection
    {
        return LunarCollection::search($search)
            ->query(function (Builder $query) {
                $query
                    ->whereHas('group', fn($query) => $query->where('handle', Handle::COLLECTION_GROUP_TAXONOMIES))
                    ->channel(StorefrontSession::getChannel())
                    ->customerGroup(StorefrontSession::getCustomerGroups());
            })->get();
    }
}
