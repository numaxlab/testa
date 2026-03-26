<?php

namespace Testa\Storefront\Queries\Editorial;

use Illuminate\Pagination\LengthAwarePaginator;
use NumaxLab\Lunar\Geslib\Models\Author;
use Testa\Storefront\Queries\ProductQueryBuilder;

final class GetAuthorProducts
{
    public function execute(Author $author, int $perPage = 12): LengthAwarePaginator
    {
        return ProductQueryBuilder::build()
            ->whereHas('authors', function ($query) use ($author) {
                $query->where((new Author)->getTable().'.id', $author->id);
            })
            ->paginate($perPage);
    }
}
