<?php

namespace Testa\Storefront\UseCases\Account;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

final class UpdateUserPassword
{
    public function execute(Authenticatable $user, string $password): void
    {
        $user->update(['password' => Hash::make($password)]);
    }
}
