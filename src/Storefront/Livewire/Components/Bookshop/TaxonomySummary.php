<?php

namespace Testa\Storefront\Livewire\Components\Bookshop;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Lunar\Models\Collection as LunarCollection;
use Testa\Storefront\Queries\ProductQueryBuilder;

class TaxonomySummary extends Component
{
    public LunarCollection $collection;
    public Collection $products;

    public function mount(): void
    {
        $this->products = ProductQueryBuilder::build()
            ->whereHas('collections', function ($query) {
                $query->whereIn(
                    (new LunarCollection)->getTable().'.id',
                    array_merge([$this->collection->id], $this->collection->descendants->pluck('id')->toArray()),
                );
            })
            ->take(6)
            ->get();
    }

    public function placeholder(): View
    {
        return view('testa::storefront.livewire.components.placeholder.products-tier');
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.bookshop.taxonomy-summary');
    }
}
