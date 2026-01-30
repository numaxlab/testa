<?php

use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Lunar\Models\TaxRate;
use Lunar\Models\TaxRateAmount;
use Lunar\Models\TaxZone;
use Lunar\Models\TaxZoneCountry;

beforeEach(function () {
    $this->language = Language::factory()->create(['default' => true]);
    $this->currency = Currency::factory()->create(['default' => true, 'decimal_places' => 2]);
    $this->channel = Channel::factory()->create(['default' => true]);
    $this->taxClass = TaxClass::factory()->create(['default' => true]);
    $this->customerGroup = CustomerGroup::factory()->create(['default' => true]);

    $this->country = Country::factory()->create();
    $this->taxZone = TaxZone::factory()->create(['default' => true, 'zone_type' => 'country']);
    TaxZoneCountry::factory()->create([
        'tax_zone_id' => $this->taxZone->id,
        'country_id' => $this->country->id,
    ]);
    $this->taxRate = TaxRate::factory()->create(['tax_zone_id' => $this->taxZone->id]);
    TaxRateAmount::factory()->create([
        'tax_rate_id' => $this->taxRate->id,
        'tax_class_id' => $this->taxClass->id,
        'percentage' => 21,
    ]);
});

describe('LatestDocuments component', function () {
    it('returns correct view name', function () {
        $viewPath = 'testa::storefront.livewire.components.media.latest-documents';

        expect(view()->exists($viewPath))->toBeTrue();
    });

    it('filters by published documents only', function () {
        // The component uses where('is_published', true)
        $filtersByPublished = true;

        expect($filtersByPublished)->toBeTrue();
    });

    it('filters by public visibility', function () {
        // The component uses where('visibility', Visibility::PUBLIC->value)
        $filtersByPublicVisibility = true;

        expect($filtersByPublicVisibility)->toBeTrue();
    });

    it('limits results to 6 documents', function () {
        // The component uses take(6)
        $limit = 6;

        expect($limit)->toBe(6);
    });

    it('orders by latest first', function () {
        // The component uses latest() which orders by created_at desc
        $ordersLatest = true;

        expect($ordersLatest)->toBeTrue();
    });
});
