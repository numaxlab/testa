<?php

namespace Testa\Storefront\Queries\Bookshop;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Lunar\Models\Product;
use Meilisearch\Endpoints\Indexes;

final class SearchProducts
{
    public function execute(
        string $term,
        ?string $taxonId,
        ?string $languageId,
        ?string $priceRange,
        ?string $availabilityId,
        int $perPage = 18,
    ): LengthAwarePaginator {
        $filters = $this->buildFilters($taxonId, $languageId, $priceRange, $availabilityId);

        return Product::search(
            $term,
            function (Indexes $search, string $query, array $options) use ($filters) {
                if (! empty($filters)) {
                    $options['filter'] = $filters;
                }

                return $search->search($query, $options);
            })
            ->query(fn(Builder $query) => $query->with([
                'variant',
                'variant.taxClass',
                'defaultUrl',
                'urls',
                'thumbnail',
                'authors',
                'prices',
            ]))
            ->paginate($perPage);
    }

    private function buildFilters(
        ?string $taxonId,
        ?string $languageId,
        ?string $priceRange,
        ?string $availabilityId,
    ): string {
        $filters = [];

        if ($taxonId) {
            $filters[] = "taxonomies.id IN [$taxonId]";
        }

        if ($languageId) {
            $filters[] = "languages.id IN [$languageId]";
        }

        if ($priceRange) {
            [$min, $max] = explode('-', $priceRange);
            $filters[] = "price >= $min AND price <= $max";
        }

        if ($availabilityId) {
            $filters[] = "geslib_status.id = $availabilityId";
        }

        return implode(' AND ', $filters);
    }
}
