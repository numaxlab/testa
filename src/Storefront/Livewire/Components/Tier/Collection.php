<?php

namespace Testa\Storefront\Livewire\Components\Tier;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Livewire\Component;
use NumaxLab\Lunar\Geslib\Handle;
use Testa\Models\Content\Tier;
use Testa\Storefront\Queries\Content\GetTierCollectionProducts;
use Testa\Storefront\Queries\Content\GetTierItineraries;

class Collection extends Component
{
    public Tier $tier;
    public ?EloquentCollection $itineraries;
    public ?EloquentCollection $products;
    private bool $isItineraries = false;

    public function mount(): void
    {
        if ($this->tier->collections->first()->group->handle === Handle::COLLECTION_GROUP_ITINERARIES) {
            $this->isItineraries = true;
            $this->itineraries = new GetTierItineraries()->execute($this->tier);

            return;
        }

        $this->products = new GetTierCollectionProducts()->execute($this->tier);
    }

    public function placeholder(): View
    {
        return view('testa::storefront.livewire.components.placeholder.products-tier');
    }

    public function render(): View
    {
        if ($this->isItineraries) {
            return view('testa::storefront.livewire.components.tier.collection-itineraries');
        }

        return view('testa::storefront.livewire.components.tier.collection-products');
    }
}
