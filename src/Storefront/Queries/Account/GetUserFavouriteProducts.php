<?php

namespace Testa\Storefront\Queries\Account;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetUserFavouriteProducts
{
    public function execute(Authenticatable $user, int $perPage = 12): LengthAwarePaginator
    {
        return $user->favourites()->paginate($perPage);
    }
}
