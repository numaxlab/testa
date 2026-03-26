<?php

namespace Testa\Storefront\Queries\Content;

use Illuminate\Database\Eloquent\Collection;
use Testa\Models\Content\Section;
use Testa\Models\Content\Tier;

final class GetTiersBySection
{
    public function execute(Section $section): Collection
    {
        return Tier::where('section', $section->value)
            ->where('is_published', true)
            ->orderBy('sort_position')
            ->get();
    }
}
