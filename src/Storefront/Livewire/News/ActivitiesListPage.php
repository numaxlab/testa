<?php

namespace Testa\Storefront\Livewire\News;

use Illuminate\View\View;
use Livewire\Attributes\Url;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Livewire\Features\WithPagination;
use Testa\Models\News\EventType;
use Testa\Storefront\Queries\News\GetActivities;

class ActivitiesListPage extends Page
{
    use WithPagination;

    #[Url]
    public string $q = '';

    #[Url]
    public string $l = '';

    #[Url]
    public string $t = '';

    public function render(): View
    {
        $eventTypes = EventType::all()->sortBy('name');

        $activities = new GetActivities()->execute($this->q, $this->t);

        return view('testa::storefront.livewire.news.activities-list', compact('eventTypes', 'activities'))
            ->title(__('Actividades'));
    }

    public function search(): void
    {
        $this->resetPage();
    }
}
