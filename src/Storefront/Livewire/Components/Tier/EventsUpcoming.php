<?php

namespace Testa\Storefront\Livewire\Components\Tier;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Testa\Models\Content\Tier;
use Testa\Storefront\Queries\News\GetLatestActivities;

class EventsUpcoming extends Component
{
    public Tier $tier;

    public Collection $activities;

    public function mount(): void
    {
        $this->activities = new GetLatestActivities()->execute();
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.tier.events-upcoming');
    }
}
