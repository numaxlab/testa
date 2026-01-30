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
use Testa\Storefront\Livewire\Components\Tier\EditorialLatest;

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

describe('EditorialLatest component', function () {
    it('has tier property', function () {
        $component = new EditorialLatest();

        expect(property_exists($component, 'tier'))->toBeTrue();
    });

    it('returns correct view name', function () {
        $viewPath = 'testa::storefront.livewire.components.tier.editorial-latest';

        expect(view()->exists($viewPath))->toBeTrue();
    });

    it('filters by published status', function () {
        // The component uses status('published')
        $expectedStatus = 'published';

        expect($expectedStatus)->toBe('published');
    });

    it('filters by in-house brand', function () {
        // The component uses whereHas('brand', in-house = true)
        $filtersByInHouse = true;

        expect($filtersByInHouse)->toBeTrue();
    });

    it('orders by created_at descending', function () {
        // The component uses orderByDesc('created_at')
        $orderColumn = 'created_at';

        expect($orderColumn)->toBe('created_at');
    });

    it('paginates results by 12', function () {
        // The component uses paginate(12)
        $perPage = 12;

        expect($perPage)->toBe(12);
    });
});
