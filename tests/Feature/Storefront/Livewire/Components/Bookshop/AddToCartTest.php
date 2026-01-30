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
use Testa\Storefront\Livewire\Components\Bookshop\AddToCart;

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

describe('AddToCart component', function () {
    it('extends base Geslib AddToCart component', function () {
        $component = new AddToCart();

        expect($component)->toBeInstanceOf(\NumaxLab\Lunar\Geslib\Storefront\Livewire\Components\AddToCart::class);
    });

    it('returns custom Testa view', function () {
        $viewPath = 'testa::storefront.livewire.components.bookshop.add-to-cart';

        expect(view()->exists($viewPath))->toBeTrue();
    });

    it('inherits purchasable property from parent', function () {
        $component = new AddToCart();

        expect(property_exists($component, 'purchasable'))->toBeTrue();
    });

    it('inherits displayPrice property from parent', function () {
        $component = new AddToCart();

        expect(property_exists($component, 'displayPrice'))->toBeTrue();
    });

    it('inherits addToCart method from parent', function () {
        $component = new AddToCart();

        expect(method_exists($component, 'addToCart'))->toBeTrue();
    });
});
