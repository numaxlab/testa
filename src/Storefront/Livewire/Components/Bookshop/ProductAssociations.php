<?php

namespace Testa\Storefront\Livewire\Components\Bookshop;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Lunar\Models\Contracts\Product;
use Testa\Storefront\Queries\Bookshop\GetProductAssociations;

class ProductAssociations extends Component
{
    public Product $product;

    public bool $isEditorialProduct = false;

    public function render(): View
    {
        return view('testa::storefront.livewire.components.bookshop.product-associations');
    }

    #[Computed]
    public function automaticAssociations(): Collection
    {
        return new GetProductAssociations()->automatic($this->product, $this->isEditorialProduct,
            $this->manualAssociations());
    }

    #[Computed]
    public function manualAssociations(): Collection
    {
        return new GetProductAssociations()->manual($this->product);
    }
}
