<?php

namespace Testa\Storefront\Queries\Checkout;

use Lunar\Shipping\Models\ShippingMethod;

final class GetShippingMethodDescription
{
    public function execute(string $code): ?string
    {
        return ShippingMethod::where('code', $code)->value('description');
    }
}
