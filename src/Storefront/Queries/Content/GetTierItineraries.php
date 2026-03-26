<?php

namespace Testa\Storefront\Queries\Content;

use Illuminate\Database\Eloquent\Collection;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Collection as LunarCollection;
use Testa\Models\Content\Tier;

final class GetTierItineraries
{
    public function execute(Tier $tier): Collection
    {
        return LunarCollection::whereIn('id', $tier->collections->pluck('id')->toArray())
            ->channel(StorefrontSession::getChannel())
            ->customerGroup(StorefrontSession::getCustomerGroups())
            ->orderBy('_lft', 'ASC')
            ->get();
    }
}
