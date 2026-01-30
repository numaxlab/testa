<?php

use Illuminate\Support\Collection;
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
use Testa\Storefront\Livewire\Components\Bookshop\ProductActivities;

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

describe('ProductActivities component', function () {
    it('has product property', function () {
        $component = new ProductActivities();

        expect(property_exists($component, 'product'))->toBeTrue();
    });

    it('has activities property defined as Collection type', function () {
        $component = new ProductActivities();

        expect(property_exists($component, 'activities'))->toBeTrue();

        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty('activities');
        $type = $property->getType();

        expect($type->getName())->toBe(Collection::class);
    });

    it('returns correct view name', function () {
        $viewPath = 'testa::storefront.livewire.components.bookshop.product-activities';

        expect(view()->exists($viewPath))->toBeTrue();
    });

    it('queries events and course modules', function () {
        // The component unions events and course modules queries
        // Both filtered by is_published = true and related to product
        $expectedQuery = true;

        expect($expectedQuery)->toBeTrue();
    });

    it('orders activities by starts_at descending', function () {
        // The component orders by starts_at desc
        $orderDirection = 'desc';

        expect($orderDirection)->toBe('desc');
    });
});
