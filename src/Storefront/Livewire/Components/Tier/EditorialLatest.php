<?php

namespace Testa\Storefront\Livewire\Components\Tier;

use Illuminate\View\View;
use Livewire\Component;
use Testa\Models\Content\Tier;
use Testa\Storefront\Queries\ProductQueryBuilder;

class EditorialLatest extends Component
{
    public Tier $tier;

    public function render(): View
    {
        $products = ProductQueryBuilder::build()
            ->whereHas('brand', function ($query) {
                $query->where('attribute_data->in-house->value', true);
            })
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('testa::storefront.livewire.components.tier.editorial-latest', compact('products'));
    }
}
