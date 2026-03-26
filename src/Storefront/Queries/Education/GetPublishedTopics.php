<?php

namespace Testa\Storefront\Queries\Education;

use Illuminate\Database\Eloquent\Collection;
use Testa\Models\Education\Topic;

final class GetPublishedTopics
{
    public function execute(): Collection
    {
        return Topic::where('is_published', true)
            ->with([
                'media',
                'defaultUrl',
            ])
            ->get();
    }
}
