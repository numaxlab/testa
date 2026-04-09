<?php

namespace Testa\Storefront\Queries\News;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Testa\Models\Education\CourseModule;
use Testa\Models\News\Event;

final class GetActivities
{
    private array $columns = ['id', 'starts_at'];

    public function execute(
        string $q = '',
        string $t = '',
        int    $perPage = 12,
    ): LengthAwarePaginator
    {
        $eventsQuery = Event::query()
            ->select([...$this->columns, DB::raw("'event' as type")])
            ->where('is_published', true)
            ->where('starts_at', '>=', now())
            ->when($q, function ($query) use ($q) {
                $query->whereIn('id', Event::search($q)->keys());
            });

        $courseModulesQuery = CourseModule::query()
            ->select([...$this->columns, DB::raw("'course-module' as type")])
            ->where('is_published', true)
            ->where('starts_at', '>=', now())
            ->when($q, function ($query) use ($q) {
                $query->whereIn('id', CourseModule::search($q)->keys());
            });

        if ($t === 'c') {
            $activities = $courseModulesQuery
                ->orderBy('starts_at', 'asc')
                ->paginate($perPage);
        } elseif (!empty($t)) {
            $activities = $eventsQuery
                ->where('event_type_id', $t)
                ->orderBy('starts_at', 'asc')
                ->paginate($perPage);
        } else {
            $activities = $eventsQuery
                ->union($courseModulesQuery)
                ->orderBy('starts_at', 'asc')
                ->paginate($perPage);
        }

        return new EagerLoadActivities()->execute($activities);
    }
}
