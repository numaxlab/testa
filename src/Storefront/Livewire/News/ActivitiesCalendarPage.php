<?php

namespace Testa\Storefront\Livewire\News;

use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Models\News\EventType;
use Testa\Storefront\Queries\News\GetActivitiesCalendar;

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

        $activitiesByDay = new GetActivitiesCalendar()->execute($this->year, $this->month, $this->t);

        return view('testa::storefront.livewire.news.activities-calendar', compact(
            'eventTypes',
            'activitiesByDay',
        ))->title(__('Calendario de actividades'));
    }
}
