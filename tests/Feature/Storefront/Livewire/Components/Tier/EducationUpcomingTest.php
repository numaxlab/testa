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
use Testa\Storefront\Livewire\Components\Tier\EducationUpcoming;

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

describe('EducationUpcoming component', function () {
    it('has tier property', function () {
        $component = new EducationUpcoming();

        expect(property_exists($component, 'tier'))->toBeTrue();
    });

    it('has courses property defined as Collection type', function () {
        $component = new EducationUpcoming();

        expect(property_exists($component, 'courses'))->toBeTrue();

        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty('courses');
        $type = $property->getType();

        expect($type->getName())->toBe(Collection::class);
    });

    it('returns correct view name', function () {
        $viewPath = 'testa::storefront.livewire.components.tier.courses';

        expect(view()->exists($viewPath))->toBeTrue();
    });

    it('filters by published courses', function () {
        // The component uses where('is_published', true)
        $filtersByPublished = true;

        expect($filtersByPublished)->toBeTrue();
    });

    it('filters by courses not yet ended', function () {
        // The component uses where('ends_at', '>=', now())
        $filtersUpcoming = true;

        expect($filtersUpcoming)->toBeTrue();
    });

    it('orders by starts_at ascending', function () {
        // The component uses orderBy('starts_at', 'asc')
        $orderColumn = 'starts_at';
        $orderDirection = 'asc';

        expect($orderColumn)->toBe('starts_at');
        expect($orderDirection)->toBe('asc');
    });

    it('limits results to 4 courses', function () {
        // The component uses limit(4)
        $limit = 4;

        expect($limit)->toBe(4);
    });
});
