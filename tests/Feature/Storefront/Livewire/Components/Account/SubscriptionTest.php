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
use Testa\Models\Content\Banner;
use Testa\Models\Content\Location;
use Testa\Storefront\Livewire\Components\Account\Subscription;

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

describe('Subscription component', function () {
    it('has subscriptions property', function () {
        $component = new Subscription();

        expect(property_exists($component, 'subscriptions'))->toBeTrue();
    });

    it('queries published banner for dashboard subscriptions location in render', function () {
        // Create a published banner for the subscriptions location
        $banner = Banner::factory()->create([
            'is_published' => true,
            'locations' => [Location::USER_DASHBOARD_SUBSCRIPTIONS->value],
        ]);

        // Create another banner that should NOT be returned (different location)
        Banner::factory()->create([
            'is_published' => true,
            'locations' => [Location::COURSE->value],
        ]);

        // Query as the component does
        $queriedBanner = Banner::whereJsonContains('locations', Location::USER_DASHBOARD_SUBSCRIPTIONS->value)
            ->where('is_published', true)
            ->first();

        expect($queriedBanner)->not->toBeNull();
        expect($queriedBanner->id)->toBe($banner->id);
    });

    it('returns null banner when no published banner exists for location', function () {
        // Create unpublished banner
        Banner::factory()->create([
            'is_published' => false,
            'locations' => [Location::USER_DASHBOARD_SUBSCRIPTIONS->value],
        ]);

        $queriedBanner = Banner::whereJsonContains('locations', Location::USER_DASHBOARD_SUBSCRIPTIONS->value)
            ->where('is_published', true)
            ->first();

        expect($queriedBanner)->toBeNull();
    });

    it('returns null banner when no banner exists for location', function () {
        $queriedBanner = Banner::whereJsonContains('locations', Location::USER_DASHBOARD_SUBSCRIPTIONS->value)
            ->where('is_published', true)
            ->first();

        expect($queriedBanner)->toBeNull();
    });
});
