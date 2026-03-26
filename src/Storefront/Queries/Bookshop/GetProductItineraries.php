<?php

namespace Testa\Storefront\Queries\Bookshop;

use Illuminate\Database\Eloquent\Collection;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Collection as LunarCollection;
use Lunar\Models\Contracts\Product;
use NumaxLab\Lunar\Geslib\Handle;

final class GetProductItineraries
{
    public function execute(Product $product): Collection
    {
        return LunarCollection::whereHas('group',
            fn($query) => $query->where('handle', Handle::COLLECTION_GROUP_ITINERARIES))
            ->whereHas('products', fn($query) => $query->where($product->getTable().'.id', $product->id))
            ->channel(StorefrontSession::getChannel())
            ->customerGroup(StorefrontSession::getCustomerGroups())
            ->orderBy('_lft', 'ASC')
            ->get();
    }
}
