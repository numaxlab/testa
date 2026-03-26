<?php

namespace Testa\Storefront\Queries\Education;

use Illuminate\Database\Eloquent\Collection;
use Testa\Models\Education\Course;

final class GetUpcomingCourses
{
    public function execute(int $limit = 4): Collection
    {
        return Course::where('is_published', true)
            ->where('ends_at', '>=', now())
            ->with([
                'media',
                'defaultUrl',
                'topic',
            ])
            ->orderBy('starts_at', 'asc')
            ->limit($limit)
            ->get();
    }
}
