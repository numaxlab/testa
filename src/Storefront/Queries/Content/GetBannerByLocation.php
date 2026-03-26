<?php

namespace Testa\Storefront\Queries\Content;

use Testa\Models\Content\Banner;
use Testa\Models\Content\Location;

final class GetBannerByLocation
{
    public function execute(Location $location): ?Banner
    {
        return Banner::whereJsonContains('locations', $location->value)
            ->where('is_published', true)
            ->with('media')
            ->first();
    }
}
