<?php

namespace Testa\Storefront\Queries\Content;

use Illuminate\Database\Eloquent\Collection;
use Testa\Models\Content\Tier;

final class GetTierEducationTopics
{
    public function execute(Tier $tier): Collection
    {
        return $tier
            ->educationTopics()
            ->with([
                'media',
                'defaultUrl',
            ])
            ->get();
    }
}
