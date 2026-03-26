<?php

namespace Testa\Storefront\Queries\Account;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;

final class GetUserLatestFavouriteProducts
{
    public function execute(Authenticatable $user, int $limit = 3): Collection
    {
        return $user->favourites()->take($limit)->get();
    }
}
