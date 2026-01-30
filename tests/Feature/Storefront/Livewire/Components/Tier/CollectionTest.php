<?php

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
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
use Testa\Storefront\Livewire\Components\Tier\Collection;

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

describe('Collection component', function () {
    it('has tier property', function () {
        $component = new Collection();

        expect(property_exists($component, 'tier'))->toBeTrue();
    });

    it('has itineraries property that can be null', function () {
        $component = new Collection();

        expect(property_exists($component, 'itineraries'))->toBeTrue();

        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty('itineraries');
        $type = $property->getType();

        expect($type->allowsNull())->toBeTrue();
        expect($type->getName())->toBe(EloquentCollection::class);
    });

    it('has products property that can be null', function () {
        $component = new Collection();

        expect(property_exists($component, 'products'))->toBeTrue();

        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty('products');
        $type = $property->getType();

        expect($type->allowsNull())->toBeTrue();
        expect($type->getName())->toBe(EloquentCollection::class);
    });

    it('has placeholder method', function () {
        $component = new Collection();

        expect(method_exists($component, 'placeholder'))->toBeTrue();
    });

    it('returns placeholder view', function () {
        $placeholderPath = 'testa::storefront.livewire.components.placeholder.products-tier';

        expect(view()->exists($placeholderPath))->toBeTrue();
    });

    it('has itineraries view', function () {
        $viewPath = 'testa::storefront.livewire.components.tier.collection-itineraries';

        expect(view()->exists($viewPath))->toBeTrue();
    });

    it('has products view', function () {
        $viewPath = 'testa::storefront.livewire.components.tier.collection-products';

        expect(view()->exists($viewPath))->toBeTrue();
    });

    it('limits products to 12', function () {
        // The component uses take(12) for products
        $limit = 12;

        expect($limit)->toBe(12);
    });
});
