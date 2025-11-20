<?php

namespace Trafikrak\Storefront\Livewire\News;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Models\Education\CourseModule;
use Trafikrak\Models\News\Event;
use Trafikrak\Models\News\EventType;

class ActivitiesListPage extends Page
{
    use WithPagination;

    #[Url]
    public string $q = '';

    #[Url]
    public string $l = '';

    #[Url]
    public string $t = '';

    private array $columns = ['id', 'starts_at'];

    public function render(): View
    {
        $eventTypes = EventType::all()->sortBy('name');

        $eventsQuery = Event::query()
            ->select([...$this->columns, DB::raw("'event' as type")])
            ->where('is_published', true)
            ->when($this->q, function ($query) {
                $videosByQuery = Event::search($this->q)->get();
                $query->whereIn('id', $videosByQuery->pluck('id'));
            });

        $courseModulesQuery = CourseModule::query()
            ->select([...$this->columns, DB::raw("'course-module' as type")])
            ->where('is_published', true)
            ->when($this->q, function ($query) {
                $videosByQuery = CourseModule::search($this->q)->get();
                $query->whereIn('id', $videosByQuery->pluck('id'));
            });

        if ($this->t === 'c') {
            $activities = $courseModulesQuery
                ->orderBy('starts_at', 'desc')
                ->paginate(12);
            $courseModulesQuery->where('location_id', $this->t);
        } else {
            if (! empty($this->t)) {
                $activities = $eventsQuery
                    ->where('event_type_id', $this->t)
                    ->orderBy('starts_at', 'desc')
                    ->paginate(12);
            } else {
                $activities = $eventsQuery
                    ->union($courseModulesQuery)
                    ->orderBy('starts_at', 'desc')
                    ->paginate(12);
            }
        }

        $activities = self::eagerLoadResults($activities);

        return view('trafikrak::storefront.livewire.news.activities-list', compact('eventTypes', 'activities'))
            ->title(__('Actividades'));
    }

    public function search(): void
    {
        $this->resetPage();
    }

    public static function eagerLoadResults(Paginator|Collection $results): Paginator|Collection
    {
        if ($results instanceof Collection) {
            $combinedCollection = $results;
        } else {
            $combinedCollection = $results->getCollection();
        }

        $eventIds = $combinedCollection->where('type', 'event')->pluck('id');
        $moduleIds = $combinedCollection->where('type', 'course-module')->pluck('id');

        $loadedEvents = Event::with(['eventType', 'defaultUrl'])
            ->whereIn('id', $eventIds)
            ->get()
            ->keyBy('id');
        $loadedModules = CourseModule::with(['course', 'course.purchasable', 'defaultUrl'])
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
