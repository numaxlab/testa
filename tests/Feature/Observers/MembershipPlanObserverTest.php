<?php

use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Testa\Models\Membership\MembershipPlan;
use Testa\Models\Membership\MembershipTier;

beforeEach(function () {
    $this->language = Language::factory()->create(['default' => true]);
    $this->currency = Currency::factory()->create(['default' => true, 'decimal_places' => 2]);
    $this->taxClass = TaxClass::factory()->create(['default' => true]);

    // Create a tier which will auto-create a product via the MembershipTierObserver
    $this->tier = MembershipTier::create([
        'name' => 'Test Tier',
        'description' => 'Test Tier Description',
    ]);
    $this->tier->refresh();
});

describe('MembershipPlanObserver created', function () {
    it('creates a product variant when a plan is created', function () {
        $plan = MembershipPlan::create([
            'membership_tier_id' => $this->tier->id,
            'name' => 'Monthly Plan',
            'description' => 'Monthly billing',
            'billing_interval' => MembershipPlan::BILLING_INTERVAL_MONTHLY,
        ]);

        $plan->refresh();
        expect($plan->variant_id)->not->toBeNull();

        $variant = ProductVariant::find($plan->variant_id);
        expect($variant)->not->toBeNull();
        expect($variant->product_id)->toBe($this->tier->purchasable_id);
        expect($variant->shippable)->toBeFalsy();
        expect($variant->purchasable)->toBe('always');
        expect($variant->sku)->toStartWith('membership-');
    });

    it('creates a price for the variant', function () {
        $plan = MembershipPlan::create([
            'membership_tier_id' => $this->tier->id,
            'name' => 'Yearly Plan',
            'description' => 'Yearly billing',
            'billing_interval' => MembershipPlan::BILLING_INTERVAL_YEARLY,
        ]);

        $plan->refresh();
        $variant = ProductVariant::find($plan->variant_id);

        expect($variant->prices)->toHaveCount(1);
        expect($variant->prices->first()->price->value)->toBe(0);
        expect($variant->prices->first()->currency_id)->toBe($this->currency->id);
    });

    it('creates an option value for the plan', function () {
        $plan = MembershipPlan::create([
            'membership_tier_id' => $this->tier->id,
            'name' => 'Basic Plan',
            'description' => 'Basic billing',
            'billing_interval' => MembershipPlan::BILLING_INTERVAL_MONTHLY,
        ]);

        $plan->refresh();
        $variant = ProductVariant::find($plan->variant_id);

        expect($variant->values)->toHaveCount(1);
    });

    it('does not create variant when tier has no product', function () {
        $tier = MembershipTier::factory()->create();
        $tier->updateQuietly(['purchasable_id' => null]);
        $tier->refresh();

        $plan = MembershipPlan::create([
            'membership_tier_id' => $tier->id,
            'name' => 'Orphan Plan',
            'description' => 'No product',
            'billing_interval' => MembershipPlan::BILLING_INTERVAL_MONTHLY,
        ]);

        $plan->refresh();
        expect($plan->variant_id)->toBeNull();
    });
});

describe('MembershipPlanObserver updated', function () {
    it('updates variant tax class when plan tax class changes', function () {
        $plan = MembershipPlan::create([
            'membership_tier_id' => $this->tier->id,
            'name' => 'Updatable Plan',
            'description' => 'Test',
            'billing_interval' => MembershipPlan::BILLING_INTERVAL_MONTHLY,
        ]);

        $newTaxClass = TaxClass::factory()->create();
        $plan->update(['tax_class_id' => $newTaxClass->id]);

        $variant = ProductVariant::find($plan->fresh()->variant_id);
        expect($variant->tax_class_id)->toBe($newTaxClass->id);
    });

    it('creates variant if it does not exist on update', function () {
        $plan = MembershipPlan::factory()->create([
            'membership_tier_id' => $this->tier->id,
        ]);
        $plan->updateQuietly(['variant_id' => null]);
        $plan->refresh();

        expect($plan->variant_id)->toBeNull();

        $plan->update(['name' => 'Trigger Update']);
        $plan->refresh();

        expect($plan->variant_id)->not->toBeNull();
    });
});

describe('MembershipPlanObserver deleted', function () {
    it('deletes variant and prices when plan is deleted', function () {
        $plan = MembershipPlan::create([
            'membership_tier_id' => $this->tier->id,
            'name' => 'To Delete',
            'description' => 'Will be deleted',
            'billing_interval' => MembershipPlan::BILLING_INTERVAL_MONTHLY,
        ]);

        $plan->refresh();
        $variantId = $plan->variant_id;

        expect(ProductVariant::find($variantId))->not->toBeNull();

        $plan->delete();

        expect(ProductVariant::find($variantId))->toBeNull();
    });

    it('handles deletion when no variant exists', function () {
        $plan = MembershipPlan::factory()->create([
            'membership_tier_id' => $this->tier->id,
        ]);
        $plan->updateQuietly(['variant_id' => null]);
        $plan->refresh();

        // Should not throw
        $plan->delete();

        expect(MembershipPlan::find($plan->id))->toBeNull();
    });
});
