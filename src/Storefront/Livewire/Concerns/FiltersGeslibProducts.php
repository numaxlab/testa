<?php

namespace Testa\Storefront\Livewire\Concerns;

use Illuminate\Support\Collection;
use Lunar\Facades\CartSession;

trait FiltersGeslibProducts
{
    protected function removeNonGeslibItems(): void
    {
        $cart = CartSession::current();

        if (!$cart) {
            return;
        }

        $geslibProductTypeId = config('lunar.geslib.product_type_id');
        $cartLines = $cart->lines()->with('purchasable.product')->get();

        $invalidLineIds = $cartLines->filter(function ($line) use ($geslibProductTypeId) {
            if ($line->purchasable_type !== 'product_variant') {
                return true;
            }

            return !$line->purchasable || $line->purchasable->product?->product_type_id !== $geslibProductTypeId;
        })->pluck('id');

        foreach ($invalidLineIds as $lineId) {
            CartSession::remove($lineId);
        }
    }

    protected function filterGeslibLines(Collection $lines): Collection
    {
        $geslibProductTypeId = config('lunar.geslib.product_type_id');

        return $lines->filter(function ($line) use ($geslibProductTypeId) {
            return $line->purchasable_type === 'product_variant'
                && $line->purchasable?->product?->product_type_id === $geslibProductTypeId;
        });
    }
}
