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
use Testa\Models\Content\Tier;
use Testa\Storefront\Livewire\Components\Tier\ArticlesLatest;
use Testa\Storefront\Livewire\Components\Tier\EditorialLatest;
use Testa\Storefront\Livewire\Components\Tier\EventsUpcoming;

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

describe('tier components on the lazy render path', function () {
    /**
     * The storefront renders every tier after the first one with `:lazy`, so Livewire paints the
     * placeholder first and renders the component from its dehydrated snapshot later. On that path
     * the view is given the component public properties and nothing else, which makes any value
     * computed inside render() invisible to it.
     */
    it('renders its view from the public properties alone', function (string $component, string $view) {
        $tier = Tier::factory()->create(['name' => 'Contrato de propiedades públicas']);

        $instance = new $component;
        $instance->tier = $tier;

        if (method_exists($instance, 'mount')) {
            $instance->mount();
        }

        $html = view($view, get_object_vars($instance))->render();

        expect($html)->toContain('Contrato de propiedades públicas');
    })->with([
        'articles-latest' => [
            ArticlesLatest::class,
            'testa::storefront.livewire.components.tier.articles-latest',
        ],
        'editorial-latest' => [
            EditorialLatest::class,
            'testa::storefront.livewire.components.tier.editorial-latest',
        ],
        'events-upcoming' => [
            EventsUpcoming::class,
            'testa::storefront.livewire.components.tier.events-upcoming',
        ],
    ]);
});
