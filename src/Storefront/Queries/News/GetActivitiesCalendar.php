<?php

namespace Testa\Storefront\Queries\News;

use Illuminate\Support\Carbon;
use Testa\Models\Education\CourseModule;
use Testa\Models\News\Event;

final class GetActivitiesCalendar
{
    public function execute(int $year, int $month, string $t = ''): array
    {
        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth()->endOfDay();

        $eventsQuery = Event::query()
            ->where('is_published', true)
            ->whereBetween('starts_at', [$startOfMonth, $endOfMonth]);

        $courseModulesQuery = CourseModule::query()
            ->where('is_published', true)
            ->whereBetween('starts_at', [$startOfMonth, $endOfMonth]);

        if ($t === 'c') {
            $events = collect();
            $courseModules = $courseModulesQuery
                ->with(['defaultUrl', 'course', 'course.defaultUrl', 'venue'])
                ->orderBy('starts_at')
                ->get();
        } elseif (! empty($t)) {
            $events = $eventsQuery
                ->where('event_type_id', $t)
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

        return collect()
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
    }
}
