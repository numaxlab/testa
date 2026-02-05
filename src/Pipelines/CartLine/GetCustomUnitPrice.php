<?php

namespace Testa\Pipelines\CartLine;

use Closure;
use Lunar\DataTypes\Price;
use Lunar\Models\CartLine;
use Lunar\Models\Contracts\CartLine as CartLineContract;

class GetCustomUnitPrice
{
    public function handle(CartLineContract $cartLine, Closure $next): mixed
    {
        /** @var CartLine $cartLine */
        $meta = $cartLine->meta;

        if (isset($meta['unit_price'])) {
            $cart = $cartLine->cart;
            $purchasable = $cartLine->purchasable;

            $cartLine->unitPrice = new Price(
                (int) $meta['unit_price'],
                $cart->currency,
                $purchasable->getUnitQuantity(),
            );

            $cartLine->unitPriceInclTax = new Price(
                (int) $meta['unit_price'],
                $cart->currency,
                $purchasable->getUnitQuantity(),
            );

            return $next($cartLine);
        }

        return $next($cartLine);
    }
}
