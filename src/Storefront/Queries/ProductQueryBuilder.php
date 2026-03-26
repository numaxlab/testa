<?php

namespace Testa\Storefront\Queries;

use Illuminate\Database\Eloquent\Builder;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Product;

final class ProductQueryBuilder
{
    public static function build(): Builder
    {
        return self::applyScopes(Product::query());
    }

    private static function applyScopes(Builder $query): Builder
    {
        return $query
            ->channel(StorefrontSession::getChannel())
            ->customerGroup(StorefrontSession::getCustomerGroups())
            ->status('published')
            ->whereHas('productType', fn(Builder $q) => $q->where('id', config('lunar.geslib.product_type_id')))
            ->with([
                'variant',
                'variant.prices',
                'variant.prices.priceable',
                'variant.prices.priceable.taxClass',
                'variant.prices.priceable.taxClass.taxRateAmounts',
                'variant.prices.currency',
                'media',
                'defaultUrl',
                'authors',
            ]);
    }

    public static function fromRelation(Builder $query): Builder
    {
        return self::applyScopes($query);
    }
}
