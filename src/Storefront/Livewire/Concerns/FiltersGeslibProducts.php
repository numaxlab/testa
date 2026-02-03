<?php

namespace Testa\Storefront\Livewire\Concerns;

use Illuminate\Support\Collection;
use Lunar\Facades\CartSession;

trait FiltersGeslibProducts
{
    private const int GESLIB_PRODUCT_TYPE_ID = 1;

    protected function removeNonGeslibItems(): void
    {
        $cart = CartSession::current();

        if (! $cart) {
            return;
        }

        $cartLines = $cart->lines()->with('purchasable.product')->get();

        $invalidLineIds = $cartLines->filter(function ($line) {
            if ($line->purchasable_type !== 'product_variant') {
                return true;
            }

            return ! $line->purchasable || $line->purchasable->product?->product_type_id !== self::GESLIB_PRODUCT_TYPE_ID;
        })->pluck('id');

        foreach ($invalidLineIds as $lineId) {
            CartSession::remove($lineId);
        }
    }

    protected function filterGeslibLines(Collection $lines): Collection
    {
        return $lines->filter(function ($line) {
            return $line->purchasable_type === 'product_variant'
                && $line->purchasable?->product?->product_type_id === self::GESLIB_PRODUCT_TYPE_ID;
        });
    }
}
