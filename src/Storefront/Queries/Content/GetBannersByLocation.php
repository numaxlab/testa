<?php

namespace Testa\Storefront\Queries\Content;

use Illuminate\Database\Eloquent\Collection;
use Testa\Models\Content\Banner;
use Testa\Models\Content\Location;

final class GetBannersByLocation
{
    public function execute(Location $location): Collection
    {
        return Banner::whereJsonContains('locations', $location->value)
            ->where('is_published', true)
            ->with('media')
            ->get();
    }
}
