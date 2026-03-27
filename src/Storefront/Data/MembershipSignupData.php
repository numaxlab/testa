<?php

namespace Testa\Storefront\Data;

final readonly class MembershipSignupData
{
    public function __construct(
        public string $membershipPlanId,
        public string $paymentType,
        public string $idNumber,
        public ?string $directDebitOwnerName,
        public ?string $directDebitBankName,
        public ?string $directDebitIban,
        public CheckoutAddressData $billingAddress,
    ) {}
}
