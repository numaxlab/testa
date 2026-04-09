<?php

namespace Testa\Pipelines\Order\Creation;

use Closure;
use Illuminate\Support\Str;
use Lunar\Models\Contracts\Order as OrderContract;
use Lunar\Models\Tag;
use Testa\Storefront\Queries\Membership\GetDonationProduct;

class TagOrder
{
    public function handle(OrderContract $order, Closure $next): mixed
    {
        $tag = null;

        foreach ($order->lines as $line) {
            if ($line->purchasable_type === 'product_variant') {
                $productTypeId = $line->purchasable->product->product_type_id;

                if ($productTypeId === config('testa.product_types.membership_tier_id')) {
                    $tag = Tag::firstOrCreate([
                        'value' => 'Subscripción socias',
                    ]);
                    break;
                }

                if (Str::contains($line->purchasable->sku, GetDonationProduct::DONATION_SKU)) {
                    $tag = Tag::firstOrCreate([
                        'value' => 'Donación',
                    ]);
                }

                if ($productTypeId === config('testa.product_types.course_id')) {
                    $tag = Tag::firstOrCreate([
                        'value' => 'Inscripción cursos',
                    ]);
                }
            }
        }

        if (!$tag) {
            $tag = Tag::firstOrCreate([
                'value' => 'Pedido librería',
            ]);
        }

        $order->tags()->attach($tag);

        return $next($order);
    }
}
