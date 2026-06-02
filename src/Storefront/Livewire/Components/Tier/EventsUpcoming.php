<?php

namespace Testa\Storefront\Livewire\Components\Tier;

use Illuminate\View\View;
use Livewire\Component;
use Testa\Models\Content\Tier;
use Testa\Storefront\Queries\News\GetLatestActivities;

class EventsUpcoming extends Component
{
    public Tier $tier;

    public function render(): View
    {
        $activities = new GetLatestActivities()->execute();

        return view('testa::storefront.livewire.components.tier.events-upcoming', compact('activities'));
    }
}
