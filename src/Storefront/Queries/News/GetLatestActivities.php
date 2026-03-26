<?php

namespace Testa\Storefront\Queries\News;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Testa\Models\Education\CourseModule;
use Testa\Models\News\Event;

final class GetLatestActivities
{
    private array $columns = ['id', 'starts_at'];

    public function execute(int $limit = 4): Collection
    {
        $eventsQuery = Event::query()
            ->select([...$this->columns, DB::raw("'event' as type")])
            ->where('is_published', true)
            ->where('starts_at', '>=', now());

        $courseModulesQuery = CourseModule::query()
            ->select([...$this->columns, DB::raw("'course-module' as type")])
            ->where('is_published', true)
            ->where('starts_at', '>=', now());

        $results = $eventsQuery
            ->union($courseModulesQuery)
            ->orderBy('starts_at', 'asc')
            ->take($limit)
            ->get();

        return new EagerLoadActivities()->execute($results);
    }
}
