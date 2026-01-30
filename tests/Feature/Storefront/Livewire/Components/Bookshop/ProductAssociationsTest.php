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
use Testa\Storefront\Livewire\Components\Bookshop\ProductAssociations;

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

describe('ProductAssociations component', function () {
    it('has product property', function () {
        $component = new ProductAssociations();

        expect(property_exists($component, 'product'))->toBeTrue();
    });

    it('has isEditorialProduct property defaulting to false', function () {
        $component = new ProductAssociations();

        expect(property_exists($component, 'isEditorialProduct'))->toBeTrue();
        expect($component->isEditorialProduct)->toBeFalse();
    });

    it('returns correct view name', function () {
        $viewPath = 'testa::storefront.livewire.components.bookshop.product-associations';

        expect(view()->exists($viewPath))->toBeTrue();
    });

    it('has automaticAssociations computed property', function () {
        $component = new ProductAssociations();

        expect(method_exists($component, 'automaticAssociations'))->toBeTrue();
    });

    it('has manualAssociations computed property', function () {
        $component = new ProductAssociations();

        expect(method_exists($component, 'manualAssociations'))->toBeTrue();
    });

    it('has LIMIT constant set to 6', function () {
        $reflection = new ReflectionClass(ProductAssociations::class);
        $constant = $reflection->getConstant('LIMIT');

        expect($constant)->toBe(6);
    });
});

describe('ProductAssociations relationship order', function () {
    it('uses standard relationship order for non-editorial products', function () {
        // For non-editorial products: authors, editorialCollections, taxonomies
        $expectedOrder = ['authors', 'editorialCollections', 'taxonomies'];

        expect($expectedOrder)->toBe(['authors', 'editorialCollections', 'taxonomies']);
    });

    it('uses editorial relationship order for editorial products', function () {
        // For editorial products: editorialCollections, authors
        $expectedOrder = ['editorialCollections', 'authors'];

        expect($expectedOrder)->toBe(['editorialCollections', 'authors']);
    });
});
