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
        string $query = '',
        string $type = '',
        int    $perPage = 12,
    ): LengthAwarePaginator
    {
        $isSearching = !empty($query) || !empty($type);

        $eventsQuery = Event::query()
            ->select([...$this->columns, DB::raw("'event' as type")])
            ->where('is_published', true)
            ->when(!$isSearching, fn($builder) => $builder->where('starts_at', '>=', now()))
            ->when($query, fn($builder) => $builder->whereIn('id', Event::search($query)->keys()));

        $courseModulesQuery = CourseModule::query()
            ->select([...$this->columns, DB::raw("'course-module' as type")])
            ->where('is_published', true)
            ->when(!$isSearching, fn($builder) => $builder->where('starts_at', '>=', now()))
            ->when($query, fn($builder) => $builder->whereIn('id', CourseModule::search($query)->keys()));

        if ($type === 'c') {
            $baseQuery = $courseModulesQuery;
        } elseif (!empty($type)) {
            $baseQuery = $eventsQuery->where('event_type_id', $type);
        } else {
            $baseQuery = $eventsQuery->union($courseModulesQuery);
        }

        if ($isSearching) {
            $activities = $baseQuery
                ->orderByRaw('CASE WHEN starts_at >= ? THEN 0 ELSE 1 END ASC', [now()])
                ->orderByRaw('CASE WHEN starts_at >= ? THEN starts_at END ASC', [now()])
                ->orderByRaw('CASE WHEN starts_at < ? THEN starts_at END DESC', [now()])
                ->paginate($perPage);
        } else {
            $activities = $baseQuery
                ->orderBy('starts_at', 'asc')
                ->paginate($perPage);
        }

        return new EagerLoadActivities()->execute($activities);
    }
}
