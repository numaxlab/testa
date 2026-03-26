<?php

namespace Testa\Storefront\Livewire\Bookshop;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Storefront\Queries\Bookshop\GetItineraries;

class ItinerariesListPage extends Page
{
    public Collection $itineraries;

    public function mount(): void
    {
        $this->itineraries = new GetItineraries()->execute();
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.bookshop.itineraries-list')
            ->title(__('Itinerarios'));
    }
}
