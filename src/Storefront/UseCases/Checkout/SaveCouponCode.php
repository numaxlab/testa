<?php

namespace Testa\Storefront\UseCases\Checkout;

use Lunar\Models\Contracts\Cart;

final class SaveCouponCode
{
    public function execute(Cart $cart, ?string $couponCode): void
    {
        $cart->coupon_code = $couponCode;
        $cart->save();
        $cart->calculate();
    }
}
