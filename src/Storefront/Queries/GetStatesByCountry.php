<?php

namespace Testa\Storefront\Queries;

use Illuminate\Database\Eloquent\Collection;
use Lunar\Models\State;

final class GetStatesByCountry
{
    public function execute(int $countryId): Collection
    {
        return State::where('country_id', $countryId)->orderBy('name')->get();
    }
}
