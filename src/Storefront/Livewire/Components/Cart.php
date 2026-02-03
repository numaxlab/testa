<?php

namespace Testa\Storefront\Livewire\Components;

use Illuminate\View\View;
use Testa\Storefront\Livewire\Concerns\AbstractCart;

class Cart extends AbstractCart
{
    public bool $linesVisible = false;

    protected $listeners = [
        'add-to-cart' => 'handleAddToCart',
    ];

    public function mount(): void
    {
        $this->removeNonGeslibItems();
        $this->mapLines();
    }

    public function handleAddToCart(): void
    {
        $this->removeNonGeslibItems();
        $this->mapLines();
        $this->linesVisible = true;
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.cart');
    }
}
