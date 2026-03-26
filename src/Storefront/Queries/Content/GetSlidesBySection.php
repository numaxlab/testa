<?php

namespace Testa\Storefront\Queries\Content;

use Illuminate\Database\Eloquent\Collection;
use Testa\Models\Content\Section;
use Testa\Models\Content\Slide;

final class GetSlidesBySection
{
    public function execute(Section $section): Collection
    {
        return Slide::where('section', $section->value)
            ->where('is_published', true)
            ->orderBy('sort_position')
            ->with('media')
            ->get();
    }
}
