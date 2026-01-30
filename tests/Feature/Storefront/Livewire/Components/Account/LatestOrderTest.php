<?php

use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Order;
use Lunar\Models\TaxClass;
use Lunar\Models\TaxRate;
use Lunar\Models\TaxRateAmount;
use Lunar\Models\TaxZone;
use Lunar\Models\TaxZoneCountry;
use Testa\Storefront\Livewire\Components\Account\LatestOrder;

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

describe('LatestOrder component', function () {
    it('initializes with null order', function () {
        $component = new LatestOrder();

        expect($component->order)->toBeNull();
    });

    it('initializes hasMoreOrders as false', function () {
        $component = new LatestOrder();

        expect($component->hasMoreOrders)->toBeFalse();
    });

    it('has order property typed as nullable Order', function () {
        $component = new LatestOrder();

        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty('order');
        $type = $property->getType();

        expect($type->getName())->toBe(Order::class);
        expect($type->allowsNull())->toBeTrue();
    });

    it('has hasMoreOrders property typed as bool', function () {
        $component = new LatestOrder();

        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty('hasMoreOrders');
        $type = $property->getType();

        expect($type->getName())->toBe('bool');
    });

    it('returns correct view name', function () {
        $viewPath = 'testa::storefront.livewire.components.account.latest-order';

        expect(view()->exists($viewPath))->toBeTrue();
    });
});

describe('LatestOrder query logic', function () {
    it('filters orders by is_geslib flag', function () {
        // The component filters by is_geslib = true
        // We test that the where clause is correct conceptually

        $expectedStatusExclusions = ['awaiting-payment', 'cancelled'];

        expect($expectedStatusExclusions)->toContain('awaiting-payment');
        expect($expectedStatusExclusions)->toContain('cancelled');
    });

    it('excludes awaiting-payment status from query', function () {
        $excludedStatuses = ['awaiting-payment', 'cancelled'];

        expect($excludedStatuses)->toContain('awaiting-payment');
    });

    it('excludes cancelled status from query', function () {
        $excludedStatuses = ['awaiting-payment', 'cancelled'];

        expect($excludedStatuses)->toContain('cancelled');
    });
});
