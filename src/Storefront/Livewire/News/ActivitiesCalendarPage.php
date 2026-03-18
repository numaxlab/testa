<?php

namespace Testa\Storefront\Livewire\News;

use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;

class ActivitiesCalendarPage extends Page
{
    public function render(): View
    {
        return view('testa::storefront.livewire.news.activities-calendar')
            ->title(__('Calendario de actividades'));
    }
}
