<?php

namespace Testa\Storefront\Queries\Bookshop;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Lunar\Models\Contracts\Product;
use Testa\Storefront\Queries\ProductQueryBuilder;

final class GetProductAssociations
{
    private const int LIMIT = 6;

    public function manual(Product $product): Collection
    {
        return $product->associations;
    }

    public function automatic(Product $product, bool $isEditorialProduct, Collection $manualAssociations): Collection
    {
        if ($manualAssociations->isNotEmpty() && $manualAssociations->count() >= self::LIMIT) {
            return new Collection();
        }

        $remainingLimit = self::LIMIT - $manualAssociations->count();

        $relationships = $isEditorialProduct
            ? ['editorialCollections', 'authors']
            : ['authors', 'editorialCollections', 'taxonomies'];

        $associations = new Collection();

        foreach ($relationships as $relationship) {
            if ($associations->count() >= $remainingLimit) {
                break;
            }

            $results = $this->byRelationship(
                product: $product,
                relationship: $relationship,
                limit: $remainingLimit - $associations->count(),
                excludeIds: $associations->pluck('id'),
            );

            $associations = $associations->merge($results);
        }

        return $associations;
    }

    private function byRelationship(
        Product $product,
        string $relationship,
        int $limit,
        Collection $excludeIds,
    ): Collection {
        $relatedIds = $product->$relationship->pluck('id');

        if ($relatedIds->isEmpty()) {
            return new Collection();
        }

        return $this
            ->baseQuery($product)
            ->whereHas($relationship, fn($query) => $query->whereKey($relatedIds))
            ->whereNotIn('id', $excludeIds)
            ->take($limit)
            ->get();
    }

    private function baseQuery(Product $product): Builder
    {
        return ProductQueryBuilder::build()
            ->where('id', '!=', $product->id);
    }
}
