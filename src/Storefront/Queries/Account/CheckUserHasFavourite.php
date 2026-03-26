<?php

namespace Testa\Storefront\Queries\Account;

use Illuminate\Contracts\Auth\Authenticatable;

final class CheckUserHasFavourite
{
    public function execute(Authenticatable $user, int $productId): bool
    {
        return $user->favourites()->where('product_id', $productId)->exists();
    }
}
