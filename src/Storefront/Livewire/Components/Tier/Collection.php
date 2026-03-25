<?php

namespace Testa\Storefront\Livewire\Components\Tier;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Livewire\Component;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Collection as LunarCollection;
use NumaxLab\Lunar\Geslib\Handle;
use Testa\Models\Content\Tier;
use Testa\Storefront\Queries\ProductQueryBuilder;

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

            $this->itineraries = LunarCollection::whereIn('id', $this->tier->collections->pluck('id')->toArray())
                ->channel(StorefrontSession::getChannel())
                ->customerGroup(StorefrontSession::getCustomerGroups())
                ->orderBy('_lft', 'ASC')
                ->get();

            return;
        }

        $this->products = ProductQueryBuilder::build()
            ->whereHas('collections', function ($query) {
                $query->whereIn(
                    (new LunarCollection)->getTable().'.id',
                    $this->tier->collections->pluck('id')->toArray(),
                );
            })
            ->take(12)
            ->get();
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
