<?php

namespace Testa\Storefront\Livewire\News;

use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Models\Education\CourseModule;
use Testa\Models\News\Event;
use Testa\Models\News\EventType;

class ActivitiesCalendarPage extends Page
{
    #[Url]
    public int $month = 0;

    #[Url]
    public int $year = 0;

    #[Url]
    public string $t = '';

    public function mount(): void
    {
        if (empty($this->month)) {
            $this->month = now()->month;
        }
        if (empty($this->year)) {
            $this->year = now()->year;
        }
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public function render(): View
    {
        $eventTypes = EventType::all()->sortBy('name');

        $startOfMonth = Carbon::create($this->year, $this->month, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth()->endOfDay();

        $eventsQuery = Event::query()
            ->where('is_published', true)
            ->whereBetween('starts_at', [$startOfMonth, $endOfMonth]);

        $courseModulesQuery = CourseModule::query()
            ->where('is_published', true)
            ->whereBetween('starts_at', [$startOfMonth, $endOfMonth]);

        if ($this->t === 'c') {
            $events = collect();
            $courseModules = $courseModulesQuery
                ->with(['defaultUrl', 'course', 'course.defaultUrl', 'venue'])
                ->orderBy('starts_at')
                ->get();
        } elseif (! empty($this->t)) {
            $events = $eventsQuery
                ->where('event_type_id', $this->t)
                ->with(['eventType', 'defaultUrl', 'venue'])
                ->orderBy('starts_at')
                ->get();
            $courseModules = collect();
        } else {
            $events = $eventsQuery
                ->with(['eventType', 'defaultUrl', 'venue'])
                ->orderBy('starts_at')
                ->get();
            $courseModules = $courseModulesQuery
                ->with(['defaultUrl', 'course', 'course.defaultUrl', 'venue'])
                ->orderBy('starts_at')
                ->get();
        }

        $activitiesByDay = collect()
            ->merge($events->map(fn(Event $event)
                => [
                'day' => $event->starts_at->day,
                'time' => $event->starts_at->format('H:i'),
                'title' => $event->name,
                'type' => 'event',
                'type_label' => $event->eventType?->name ?? __('Evento'),
                'venue' => $event->venue?->name,
                'url' => route('testa.storefront.events.show', $event->defaultUrl->slug),
            ]))
            ->merge($courseModules->map(fn(CourseModule $module)
                => [
                'day' => $module->starts_at->day,
                'time' => $module->starts_at->format('H:i'),
                'title' => $module->name,
                'type' => 'course-module',
                'type_label' => __('Formación'),
                'venue' => $module->venue?->name,
                'url' => route('testa.storefront.education.courses.modules.show', [
                    $module->course->defaultUrl->slug,
                    $module->defaultUrl->slug,
                ]),
            ]))
            ->sortBy('time')
            ->groupBy('day')
            ->toArray();

        return view('testa::storefront.livewire.news.activities-calendar', compact(
            'eventTypes',
            'activitiesByDay',
        ))->title(__('Calendario de actividades'));
    }
}
