<?php

namespace Testa\Storefront\Queries\Membership;

use Lunar\Models\Product;

final class GetDonationProduct
{
    public const string DONATION_SKU = 'donation';

    public function execute(): Product
    {
        return Product::whereHas('variants', function ($query) {
            $query->where('sku', self::DONATION_SKU);
        })->with([
            'variants.basePrices.currency',
            'variants.basePrices.priceable',
            'variants.values.option',
        ])->firstOrFail();
    }
}
