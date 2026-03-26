<?php

namespace Testa\Storefront\Queries\Checkout;

use Illuminate\Database\Eloquent\Collection;
use Lunar\Shipping\Models\ShippingMethod;

final class GetPickupShippingMethods
{
    public function execute(): Collection
    {
        return ShippingMethod::where('driver', 'collection')->get();
    }
}
