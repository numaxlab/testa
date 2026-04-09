<?php

namespace Testa\Storefront\Queries\Bookshop;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Lunar\Models\Collection;
use Lunar\Models\Product;
use Testa\Storefront\Queries\ProductQueryBuilder;

final class GetSectionProducts
{
    public function execute(
        Collection $section,
        string     $q = '',
        string     $t = '',
        int        $perPage = 18,
    ): LengthAwarePaginator
    {
        $query = ProductQueryBuilder::build()
            ->withCount('media')
            ->orderByDesc('media_count');

        $this->applySearchFilter($query, $q);
        $this->applyCollectionFilter($query, $section, $t);

        return $query->paginate($perPage);
    }

    private function applySearchFilter(Builder $query, string $q): void
    {
        if (!$q) {
            return;
        }

        $productsByQuery = Product::search($q)->get();

        if ($productsByQuery->isEmpty()) {
            $query->whereRaw('0 = 1');

            return;
        }

        $query->whereIn('id', $productsByQuery->pluck('id'));
    }

    private function applyCollectionFilter(Builder $query, Collection $section, string $t): void
    {
        if ($t) {
            $t = (int)$t;
            $collection = Collection::findOrFail($t);
            $collectionTable = (new Collection)->getTable();

            if ($collection->getDescendantCount() > 0) {
                $descendantIds = $collection->descendants->pluck('id');
                $query->whereHas('collections', function (Builder $q) use ($collectionTable, $descendantIds) {
                    $q->whereIn($collectionTable . '.id', $descendantIds);
                });
            } else {
                $query->whereHas('collections', function (Builder $q) use ($collectionTable, $t) {
                    $q->where($collectionTable . '.id', $t);
                });
            }

            return;
        }

        $descendantIds = $section->descendants->pluck('id');

        $query->whereHas('collections', function (Builder $q) use ($descendantIds) {
            $q->whereIn((new Collection)->getTable() . '.id', $descendantIds);
        });
    }
}
