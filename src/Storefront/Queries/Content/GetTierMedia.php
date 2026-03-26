<?php

namespace Testa\Storefront\Queries\Content;

use Illuminate\Database\Eloquent\Collection;
use Testa\Models\Content\Tier;
use Testa\Models\Media\Visibility;

final class GetTierMedia
{
    public function execute(Tier $tier): Collection
    {
        return $tier
            ->attachments()
            ->whereHas('media', function ($query) {
                $query
                    ->where('is_published', true)
                    ->where('visibility', Visibility::PUBLIC->value);
            })
            ->with(['media'])
            ->get();
    }
}
