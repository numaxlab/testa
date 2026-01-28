<?php

namespace Testa\Observers;

use Illuminate\Support\Facades\DB;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Testa\Models\Membership\MembershipPlan;

class MembershipPlanObserver
{
    public function created(MembershipPlan $plan): void
    {
        $this->createVariant($plan);
    }

    private function createVariant(MembershipPlan $plan): void
    {
        $tier = $plan->tier;
        $product = $tier->purchasable;

        if (! $product) {
            return;
        }

        $taxClass = $plan->tax_class_id
            ? TaxClass::find($plan->tax_class_id)
            : TaxClass::where('default', true)->first();

        $currency = Currency::where('default', true)->first();

        $language = Language::where('default', true)->first();

        $productOption = $product->productOptions->first();

        DB::beginTransaction();

        $optionValue = $productOption->values()->create([
            'name' => [
                $language->code => $plan->name,
            ],
            'position' => $productOption->values->count() + 1,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'tax_class_id' => $taxClass?->id,
            'sku' => 'membership-'.$product->id.'-'.$optionValue->id,
            'shippable' => false,
            'stock' => 0,
            'unit_quantity' => 1,
            'min_quantity' => 1,
            'quantity_increment' => 1,
            'backorder' => 0,
            'purchasable' => 'always',
        ]);

        $variant->values()->attach($optionValue);

        $variant->prices()->create([
            'price' => 0,
            'compare_price' => 0,
            'currency_id' => $currency->id,
            'min_quantity' => 1,
            'customer_group_id' => null,
        ]);

        $plan->updateQuietly(['variant_id' => $variant->id]);

        DB::commit();
    }

    public function updated(MembershipPlan $plan): void
    {
        $variant = $plan->variant;

        if (! $variant) {
            $this->createVariant($plan);

            return;
        }

        if ($plan->isDirty('tax_class_id')) {
            $variant->update([
                'tax_class_id' => $plan->tax_class_id,
            ]);
        }
    }

    public function deleted(MembershipPlan $plan): void
    {
        $variant = $plan->variant;

        if (! $variant) {
            return;
        }

        $variant->prices()->delete();
        $variant->delete();
    }
}
