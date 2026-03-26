<?php

namespace Testa\Storefront\Queries\Bookshop;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Lunar\Models\Collection;
use Lunar\Models\Product;
use Testa\Storefront\Queries\ProductQueryBuilder;

final class GetTopicProducts
{
    public function execute(Collection $topic, string $q = '', int $perPage = 18): LengthAwarePaginator
    {
        $query = ProductQueryBuilder::build()
            ->whereHas('collections', function ($query) use ($topic) {
                $query->where((new Collection)->getTable().'.id', $topic->id);
            })
            ->withCount('media')
            ->orderByDesc('media_count');

        if ($q) {
            $productsByQuery = Product::search($q)->get();

            $query->whereIn('id', $productsByQuery->pluck('id'));
        }

        return $query->paginate($perPage);
    }
}
