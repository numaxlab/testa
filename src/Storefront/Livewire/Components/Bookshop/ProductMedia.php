<?php

namespace Testa\Storefront\Livewire\Components\Bookshop;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Contracts\Product;
use Testa\Storefront\Queries\Bookshop\GetProductAttachments;

class ProductMedia extends Component
{
    public Product $product;

    public Collection $attachments;

    public function mount(): void
    {
        $this->attachments = new GetProductAttachments()->execute($this->product);
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.bookshop.product-media');
    }
}
