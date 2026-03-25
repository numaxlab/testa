<?php

namespace Testa\Storefront\UseCases\Account;

use Illuminate\Contracts\Auth\Authenticatable;

final class DeleteUser
{
    public function execute(Authenticatable $user): void
    {
        $user->delete();
    }
}
