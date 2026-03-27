<?php

namespace Testa\Storefront\Data;

final readonly class RegisterUserData
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $email,
        public string $password,
    ) {}
}
