<?php

namespace Testa\Storefront\Queries\Content;

use Illuminate\Database\Eloquent\Collection;
use Testa\Models\Content\Tier;

final class GetTierCourses
{
    public function execute(Tier $tier): Collection
    {
        return $tier
            ->courses()
            ->with([
                'media',
                'defaultUrl',
                'topic',
            ])
            ->orderBy('starts_at', 'asc')
            ->get();
    }
}
