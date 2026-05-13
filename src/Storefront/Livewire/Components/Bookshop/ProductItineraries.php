<?php

namespace Testa\Storefront\Livewire\Components\Bookshop;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Contracts\Product;
use Testa\Storefront\Queries\Bookshop\GetProductItineraries;

class ProductItineraries extends Component
{
    public Product $product;

    public Collection $itineraries;

    public function placeholder(): View
    {
        return view('testa::storefront.livewire.components.placeholder.empty');
    }

    public function mount(): void
    {
        $this->itineraries = new GetProductItineraries()->execute($this->product);
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.bookshop.product-itineraries');
    }
}
