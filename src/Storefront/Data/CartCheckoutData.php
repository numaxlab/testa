<?php

namespace Testa\Storefront\Data;

final readonly class CartCheckoutData
{
    public function __construct(
        public string $paymentType,
        public string $shippingMethod,
        public bool   $wantsInvoice,
        public bool   $isGift,
    )
    {
    }
}
