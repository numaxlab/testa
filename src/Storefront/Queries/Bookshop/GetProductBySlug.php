<?php

namespace Testa\Storefront\Queries\Bookshop;

use Lunar\Facades\StorefrontSession;
use Lunar\Models\Product;

final class GetProductBySlug
{
    public function execute(string $slug): Product
    {
        return Product::channel(StorefrontSession::getChannel())
            ->customerGroup(StorefrontSession::getCustomerGroups())
            ->status('published')
            ->whereHas('productType', function ($query) {
                $query->where('id', config('lunar.geslib.product_type_id'));
            })
            ->whereHas('urls', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })
            ->with([
                'variant',
                'variant.prices',
                'variant.prices.priceable',
                'variant.prices.priceable.taxClass',
                'variant.prices.priceable.taxClass.taxRateAmounts',
                'variant.prices.currency',
                'media',
                'taxonomies',
                'taxonomies.ancestors',
                'editorialCollections',
                'languages',
                'statuses',
                'brand',
            ])
            ->firstOrFail();
    }
}
