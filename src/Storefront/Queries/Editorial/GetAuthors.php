<?php

namespace Testa\Storefront\Queries\Editorial;

use Illuminate\Pagination\LengthAwarePaginator;
use NumaxLab\Lunar\Geslib\Models\Author;

final class GetAuthors
{
    public function execute(int $perPage = 32): LengthAwarePaginator
    {
        return Author::whereHas('products', function ($query) {
            $query->whereHas('brand', function ($query) {
                $query->where('attribute_data->in-house->value', true);
            });
        })
            ->orderBy('name', 'ASC')
            ->with(['defaultUrl', 'media'])
            ->paginate($perPage);
    }
}
