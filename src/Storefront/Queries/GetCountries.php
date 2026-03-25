<?php

namespace Testa\Storefront\Queries;

use Illuminate\Database\Eloquent\Collection;
use Lunar\Models\Country;

final class GetCountries
{
    public function execute(): Collection
    {
        return Country::orderBy('native')->get();
    }
}
