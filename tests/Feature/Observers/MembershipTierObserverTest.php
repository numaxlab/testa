<?php

use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Testa\Models\Membership\MembershipTier;
use Testa\Observers\MembershipTierObserver;

beforeEach(function () {
    $this->language = Language::factory()->create(['default' => true]);
    $this->currency = Currency::factory()->create(['default' => true, 'decimal_places' => 2]);
    $this->taxClass = TaxClass::factory()->create(['default' => true]);
});

describe('MembershipTierObserver created', function () {
    it('creates a Lunar product when a tier is created', function () {
        $tier = MembershipTier::create([
            'name' => 'Gold Tier',
            'description' => 'Premium membership',
        ]);

        $tier->refresh();
        expect($tier->purchasable_id)->not->toBeNull();

        $product = Product::find($tier->purchasable_id);
        expect($product)->not->toBeNull();
        expect($product->product_type_id)->toBe(MembershipTierObserver::PRODUCT_TYPE_ID);
        expect($product->status)->toBe('published');
    });

    it('creates a product option for the tier', function () {
        $tier = MembershipTier::create([
            'name' => 'Silver Tier',
            'description' => 'Standard membership',
        ]);

        $tier->refresh();
        $product = Product::find($tier->purchasable_id);

        expect($product->productOptions)->toHaveCount(1);

        $option = $product->productOptions->first();
        expect($option->handle)->toStartWith('membership-tier-');
        expect($option->shared)->toBeFalse();
    });
});

describe('MembershipTierObserver updated', function () {
    it('updates product name when tier name changes', function () {
        $tier = MembershipTier::create([
            'name' => 'Original Name',
            'description' => 'Description',
        ]);

        $tier->update(['name' => 'Updated Name']);

        $product = Product::find($tier->fresh()->purchasable_id);
        expect($product->translateAttribute('name'))->toBe('Updated Name');
    });

    it('creates product if it does not exist on update', function () {
        $tier = MembershipTier::factory()->create();
        $tier->updateQuietly(['purchasable_id' => null]);
        $tier->refresh();

        expect($tier->purchasable_id)->toBeNull();

        $tier->update(['name' => 'Trigger Update']);
        $tier->refresh();

        expect($tier->purchasable_id)->not->toBeNull();
    });
});

describe('MembershipTierObserver deleted', function () {
    it('deletes product and variants when tier is deleted', function () {
        $tier = MembershipTier::create([
            'name' => 'To Delete',
            'description' => 'Will be deleted',
        ]);

        $tier->refresh();
        $productId = $tier->purchasable_id;

        expect(Product::find($productId))->not->toBeNull();

        $tier->delete();

        expect(Product::find($productId))->toBeNull();
    });

    it('handles deletion when no product exists', function () {
        $tier = MembershipTier::factory()->create();
        $tier->updateQuietly(['purchasable_id' => null]);
        $tier->refresh();

        // Should not throw
        $tier->delete();

        expect(MembershipTier::find($tier->id))->toBeNull();
    });
});
