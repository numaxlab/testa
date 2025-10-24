<?php

namespace Trafikrak\Storefront\Livewire\Components\Bookshop;

use Illuminate\View\View;

class AddToCart extends \NumaxLab\Lunar\Geslib\Storefront\Livewire\Components\AddToCart
{
    public function render(): View
    {
        return view('trafikrak::storefront.livewire.components.bookshop.add-to-cart');
    }
}
