<?php

namespace Testa\Storefront\UseCases\Account;

use Illuminate\Contracts\Auth\Authenticatable;

final class RemoveFavouriteProduct
{
    public function execute(Authenticatable $user, int $productId): void
    {
        $user->favourites()->detach($productId);
    }
}
