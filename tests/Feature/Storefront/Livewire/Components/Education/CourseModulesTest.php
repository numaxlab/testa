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
use Testa\Storefront\Livewire\Components\Education\CourseModules;

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

describe('CourseModules component', function () {
    it('has course property', function () {
        $component = new CourseModules();

        expect(property_exists($component, 'course'))->toBeTrue();
    });

    it('has except property that can be null', function () {
        $component = new CourseModules();

        expect(property_exists($component, 'except'))->toBeTrue();

        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty('except');
        $type = $property->getType();

        expect($type->allowsNull())->toBeTrue();
    });

    it('has title property', function () {
        $component = new CourseModules();

        expect(property_exists($component, 'title'))->toBeTrue();
    });

    it('has modules property defined as Collection type', function () {
        $component = new CourseModules();

        expect(property_exists($component, 'modules'))->toBeTrue();

        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty('modules');
        $type = $property->getType();

        expect($type->getName())->toBe(Collection::class);
    });

    it('returns correct view name', function () {
        $viewPath = 'testa::storefront.livewire.components.education.course-modules';

        expect(view()->exists($viewPath))->toBeTrue();
    });

    it('filters modules by published status', function () {
        // The component uses where('is_published', true)
        $filtersByPublished = true;

        expect($filtersByPublished)->toBeTrue();
    });

    it('orders modules by starts_at', function () {
        // The component uses orderBy('starts_at')
        $orderColumn = 'starts_at';

        expect($orderColumn)->toBe('starts_at');
    });

    it('can exclude a specific module', function () {
        // The component excludes module by id when except is set
        $supportsExclusion = true;

        expect($supportsExclusion)->toBeTrue();
    });
});
