<?php

namespace Testa\Storefront\Queries\Bookshop;

use Illuminate\Database\Eloquent\Collection;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Collection as LunarCollection;
use NumaxLab\Lunar\Geslib\Handle;

final class GetItineraries
{
    public function execute(): Collection
    {
        return LunarCollection::whereHas('group', function ($query) {
            $query->where('handle', Handle::COLLECTION_GROUP_ITINERARIES);
        })->channel(StorefrontSession::getChannel())
            ->customerGroup(StorefrontSession::getCustomerGroups())
            ->orderBy('_lft', 'ASC')
            ->get();
    }
}
