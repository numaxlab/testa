<?php

namespace Testa\Storefront\Queries\Media;

use Illuminate\Database\Eloquent\Collection;
use Testa\Models\Media\Document;
use Testa\Models\Media\Visibility;

final class GetLatestPublicDocuments
{
    public function execute(int $limit = 6): Collection
    {
        return Document::where('is_published', true)
            ->where('visibility', Visibility::PUBLIC->value)
            ->latest()
            ->take($limit)
            ->get();
    }
}
