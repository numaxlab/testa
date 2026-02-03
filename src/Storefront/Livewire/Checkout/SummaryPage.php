<?php

namespace Testa\Storefront\Livewire\Checkout;

use Illuminate\View\View;
use Testa\Storefront\Livewire\Concerns\AbstractCart;

class SummaryPage extends AbstractCart
{
    public function mount(): void
    {
        if (! $this->cart || $this->cart->lines->isEmpty()) {
            $this->redirect('/');

            return;
        }

        $this->removeNonGeslibItems();
        $this->mapLines();
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.checkout.summary');
    }
}
