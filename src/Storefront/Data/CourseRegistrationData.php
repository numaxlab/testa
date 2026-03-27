<?php

namespace Testa\Storefront\Data;

final readonly class CourseRegistrationData
{
    public function __construct(
        public string $selectedVariantId,
        public string $paymentType,
        public bool $invoice,
        public ?CheckoutAddressData $billingAddress,
    ) {}
}
