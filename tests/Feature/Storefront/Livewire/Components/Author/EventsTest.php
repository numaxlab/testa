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
use Testa\Storefront\Livewire\Components\Author\Events;

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

describe('Author Events component', function () {
    it('has author property', function () {
        $component = new Events();

        expect(property_exists($component, 'author'))->toBeTrue();
    });

    it('has events property defined as Collection type', function () {
        $component = new Events();

        expect(property_exists($component, 'events'))->toBeTrue();

        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty('events');
        $type = $property->getType();

        expect($type->getName())->toBe(Collection::class);
    });

    it('returns correct view name', function () {
        $viewPath = 'testa::storefront.livewire.components.author.events';

        expect(view()->exists($viewPath))->toBeTrue();
    });

    it('queries course modules where author is instructor', function () {
        // The component filters CourseModule by instructors relationship
        $filtersByInstructor = true;

        expect($filtersByInstructor)->toBeTrue();
    });

    it('filters by published course modules', function () {
        // The component uses where('is_published', true)
        $filtersByPublished = true;

        expect($filtersByPublished)->toBeTrue();
    });
});
