<?php

namespace Testa\Storefront\Queries\News;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Testa\Models\Education\CourseModule;
use Testa\Models\News\Event;

final class EagerLoadActivities
{
    public function execute(Paginator|Collection $results): Paginator|Collection
    {
        if ($results instanceof Collection) {
            $combinedCollection = $results;
        } else {
            $combinedCollection = $results->getCollection();
        }

        $eventIds = $combinedCollection->where('type', 'event')->pluck('id');
        $moduleIds = $combinedCollection->where('type', 'course-module')->pluck('id');

        $loadedEvents = Event::with(['eventType', 'defaultUrl', 'media'])
            ->whereIn('id', $eventIds)
            ->get()
            ->keyBy('id');
        $loadedModules = CourseModule::with(['defaultUrl', 'course', 'course.purchasable', 'course.defaultUrl'])
            ->whereIn('id', $moduleIds)
            ->get()
            ->keyBy('id');

        $finalCollection = $combinedCollection->map(function ($item) use ($loadedEvents, $loadedModules) {
            if ($item->type === 'event' && $loadedEvents->has($item->id)) {
                return $loadedEvents->get($item->id);
            }
            if ($item->type === 'course-module' && $loadedModules->has($item->id)) {
                return $loadedModules->get($item->id);
            }

            return $item;
        });

        if ($results instanceof Collection) {
            return $finalCollection;
        }

        $results->setCollection($finalCollection);

        return $results;
    }
}
