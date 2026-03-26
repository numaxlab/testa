<?php

namespace Testa\Storefront\Queries\Education;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Testa\Models\Education\Course;

final class GetCourses
{
    public function execute(
        string $q = '',
        string $topicId = '',
        string $deliveryMethod = '',
        int $perPage = 12,
    ): LengthAwarePaginator {
        $query = Course::where('is_published', true)
            ->with([
                'horizontalImage',
                'verticalImage',
                'defaultUrl',
                'topic',
            ])
            ->orderBy('ends_at', 'desc')
            ->when($deliveryMethod, fn($q) => $q->where('delivery_method', $deliveryMethod));

        if ($q) {
            $coursesByQuery = Course::search($q)->take(PHP_INT_MAX)->get();
            $query->whereIn('id', $coursesByQuery->pluck('id'));
        }

        if ($topicId) {
            $query->whereHas('topic', fn($q) => $q->where('id', $topicId));
        }

        return $query->paginate($perPage);
    }
}
