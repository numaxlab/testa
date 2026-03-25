<?php

namespace Testa\Storefront\Data;

final readonly class UpdateProfileData
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $email,
        public ?string $tax_identifier,
        public ?string $company_name,
    ) {}
}
