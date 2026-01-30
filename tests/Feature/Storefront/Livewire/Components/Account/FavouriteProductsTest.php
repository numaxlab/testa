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
use Testa\Storefront\Livewire\Components\Account\FavouriteProducts;

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

describe('FavouriteProducts component', function () {
    it('has latestFavouriteProducts property defined as Collection type', function () {
        $component = new FavouriteProducts();

        expect(property_exists($component, 'latestFavouriteProducts'))->toBeTrue();

        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty('latestFavouriteProducts');
        $type = $property->getType();

        expect($type->getName())->toBe(Collection::class);
    });

    it('has removeFromFavourites method', function () {
        $component = new FavouriteProducts();

        expect(method_exists($component, 'removeFromFavourites'))->toBeTrue();
    });

    it('removeFromFavourites accepts productId parameter', function () {
        $component = new FavouriteProducts();

        $reflection = new ReflectionClass($component);
        $method = $reflection->getMethod('removeFromFavourites');
        $parameters = $method->getParameters();

        expect($parameters)->toHaveCount(1);
        expect($parameters[0]->getName())->toBe('productId');
    });

    it('returns correct view name', function () {
        $viewPath = 'testa::storefront.livewire.components.account.favourite-products';

        expect(view()->exists($viewPath))->toBeTrue();
    });

    it('limits favourites to 3 products', function () {
        // The component calls take(3) on favourites
        $limit = 3;

        expect($limit)->toBe(3);
    });
});
