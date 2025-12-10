<?php

namespace Testa\Storefront\Livewire\Components\Tier;

use Illuminate\View\View;
use Livewire\Component;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Product;
use Testa\Models\Content\Tier;

class EditorialLatest extends Component
{
    public Tier $tier;

    public function render(): View
    {
        $products = Product::channel(StorefrontSession::getChannel())
            ->customerGroup(StorefrontSession::getCustomerGroups())
            ->status('published')
            ->whereHas('productType', function ($query) {
                $query->where('id', config('lunar.geslib.product_type_id'));
            })
            ->whereHas('brand', function ($query) {
                $query->where('attribute_data->in-house->value', true);
            })
            ->with([
                'variant',
                'variant.prices',
                'variant.prices.priceable',
                'variant.prices.priceable.taxClass',
                'variant.prices.priceable.taxClass.taxRateAmounts',
                'variant.prices.currency',
                'media',
                'defaultUrl',
                'authors',
            ])
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('testa::storefront.livewire.components.tier.editorial-latest', compact('products'));
    }
}
