<?php

// Integration test for MembershipPlan::priceCents() — requires Lunar's full
// ProductVariant + Price infrastructure, so it lives in Feature rather than Unit.

use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Price;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Testa\Models\Membership\MembershipPlan;

beforeEach(function () {
    Language::factory()->create(['default' => true]);
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true, 'decimal_places' => 2, 'exchange_rate' => 1]);
    Channel::factory()->create(['default' => true]);
    CustomerGroup::factory()->create(['default' => true]);
});

it('priceCents returns base price in cents from the linked ProductVariant', function () {
    $currency = Currency::first();
    $variant = ProductVariant::factory()->create();

    // Insert the price record directly on the variant.
    // The Lunar prices table uses the configured table_prefix (default: 'lunar_').
    $pricesTable = config('lunar.database.table_prefix', 'lunar_').'prices';
    \Illuminate\Support\Facades\DB::table($pricesTable)->insert([
        'currency_id' => $currency->id,
        'priceable_type' => $variant->getMorphClass(),
        'priceable_id' => $variant->id,
        'price' => 2500,
        'compare_price' => null,
        'customer_group_id' => null,
        'min_quantity' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // MembershipPlanObserver::created() may overwrite variant_id when the tier
    // has a purchasable product. Use createQuietly() to bypass observer and
    // then forcefully assign our test variant.
    $plan = MembershipPlan::factory()->createQuietly(['variant_id' => $variant->id]);

    expect($plan->priceCents())->toBe(2500);
});

it('priceCents returns 0 when the variant has no base price configured', function () {
    $variant = ProductVariant::factory()->create();

    // No Price record created — variant exists but has no prices.
    $plan = MembershipPlan::factory()->create(['variant_id' => $variant->id]);

    expect($plan->priceCents())->toBe(0);
});
