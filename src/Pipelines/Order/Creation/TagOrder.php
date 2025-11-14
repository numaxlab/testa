<?php

namespace Trafikrak\Pipelines\Order\Creation;

use Closure;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Lunar\Models\Contracts\Order as OrderContract;
use Lunar\Models\Tag;
use Trafikrak\Models\Membership\MembershipPlan;
use Trafikrak\Storefront\Livewire\Membership\DonatePage;

class TagOrder
{
    public function handle(OrderContract $order, Closure $next): mixed
    {
        $tag = null;

        foreach ($order->lines as $line) {
            if ($line->purchasable_type === Relation::getMorphAlias(MembershipPlan::class)) {
                $tag = Tag::firstOrCreate([
                    'value' => 'Subscripción socias',
                ]);
                break;
            }

            if ($line->purchasable_type === 'product_variant') {
                if (Str::contains($line->purchasable->sku, DonatePage::DONATION_PRODUCT_SKU)) {
                    $tag = Tag::firstOrCreate([
                        'value' => 'Donación',
                    ]);
                }
            }
        }

        if (! $tag) {
            $tag = Tag::firstOrCreate([
                'value' => 'Pedido librería',
            ]);
        }

        $order->tags()->attach($tag);

        return $next($order);
    }
}