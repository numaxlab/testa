<?php

namespace Testa\Storefront\Livewire\Components\Bookshop;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Contracts\Product;
use Testa\Storefront\Queries\News\GetProductActivities;

class ProductActivities extends Component
{
    public Product $product;

    public Collection $activities;

    public function mount(): void
    {
        $this->activities = new GetProductActivities()->execute($this->product);
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.bookshop.product-activities');
    }
}
