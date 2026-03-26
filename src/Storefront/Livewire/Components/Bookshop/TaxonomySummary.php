<?php

namespace Testa\Storefront\Livewire\Components\Bookshop;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Lunar\Models\Collection as LunarCollection;
use Testa\Storefront\Queries\Bookshop\GetTaxonomyProducts;

class TaxonomySummary extends Component
{
    public LunarCollection $collection;
    public Collection $products;

    public function mount(): void
    {
        $this->products = new GetTaxonomyProducts()->execute($this->collection);
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
