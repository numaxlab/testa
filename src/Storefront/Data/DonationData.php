<?php

namespace Testa\Storefront\Data;

final readonly class DonationData
{
    public function __construct(
        public string $selectedQuantity,
        public ?float $freeQuantityValue,
        public string $paymentType,
        public string $idNumber,
        public string $comments,
        public string $directDebitOwnerName = '',
        public string $directDebitBankName = '',
        public string $directDebitIban = '',
    ) {}
}
