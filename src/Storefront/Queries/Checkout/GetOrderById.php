<?php

namespace Testa\Storefront\Queries\Checkout;

use Lunar\Models\Order;

final class GetOrderById
{
    public function execute(int $id, string $fingerprint): Order
    {
        return Order::where('id', $id)
            ->where('fingerprint', $fingerprint)
            ->with(['shippingAddress'])
            ->firstOrFail();
    }
}
