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
use Testa\Storefront\Livewire\Components\Bookshop\ProductAvailability;

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

describe('ProductAvailability component', function () {
    it('has purchasable property that can be null', function () {
        $component = new ProductAvailability();

        expect(property_exists($component, 'purchasable'))->toBeTrue();
        expect($component->purchasable)->toBeNull();
    });

    it('has status property defaulting to empty string', function () {
        $component = new ProductAvailability();

        expect(property_exists($component, 'status'))->toBeTrue();
        expect($component->status)->toBe('');
    });

    it('has moreInfo property defaulting to empty string', function () {
        $component = new ProductAvailability();

        expect(property_exists($component, 'moreInfo'))->toBeTrue();
        expect($component->moreInfo)->toBe('');
    });

    it('returns correct view name', function () {
        $viewPath = 'testa::storefront.livewire.components.bookshop.product-availability';

        expect(view()->exists($viewPath))->toBeTrue();
    });

    it('has determineStatus method', function () {
        $component = new ProductAvailability();

        expect(method_exists($component, 'determineStatus'))->toBeTrue();
    });

    it('has getStockByCenterData method', function () {
        $component = new ProductAvailability();

        expect(method_exists($component, 'getStockByCenterData'))->toBeTrue();
    });

    it('has buildStockByCenterInfo method', function () {
        $component = new ProductAvailability();

        expect(method_exists($component, 'buildStockByCenterInfo'))->toBeTrue();
    });
});

describe('ProductAvailability status logic', function () {
    it('returns Disponible for in_stock mode with stock', function () {
        $component = new ProductAvailability();
        $reflection = new ReflectionClass($component);
        $method = $reflection->getMethod('determineStatus');
        $method->setAccessible(true);

        $status = $method->invoke($component, 'in_stock', true);

        expect($status)->toBe(__('Disponible'));
    });

    it('returns No disponible for in_stock mode without stock', function () {
        $component = new ProductAvailability();
        $reflection = new ReflectionClass($component);
        $method = $reflection->getMethod('determineStatus');
        $method->setAccessible(true);

        $status = $method->invoke($component, 'in_stock', false);

        expect($status)->toBe(__('No disponible'));
    });

    it('returns Disponible for always mode with stock', function () {
        $component = new ProductAvailability();
        $reflection = new ReflectionClass($component);
        $method = $reflection->getMethod('determineStatus');
        $method->setAccessible(true);

        $status = $method->invoke($component, 'always', true);

        expect($status)->toBe(__('Disponible'));
    });

    it('returns Disponible bajo pedido for always mode without stock', function () {
        $component = new ProductAvailability();
        $reflection = new ReflectionClass($component);
        $method = $reflection->getMethod('determineStatus');
        $method->setAccessible(true);

        $status = $method->invoke($component, 'always', false);

        expect($status)->toBe(__('Disponible bajo pedido'));
    });
});

describe('ProductAvailability stock by center', function () {
    it('builds stock info with available centers', function () {
        $component = new ProductAvailability();
        $reflection = new ReflectionClass($component);
        $method = $reflection->getMethod('buildStockByCenterInfo');
        $method->setAccessible(true);

        $data = [
            'Madrid' => 5,
            'Barcelona' => 0,
            'Valencia' => 3,
        ];

        $info = $method->invoke($component, $data);

        // Should only include centers with quantity > 0
        expect($info)->toContain('Madrid');
        expect($info)->toContain('Valencia');
        expect($info)->not->toContain('Barcelona');
    });

    it('returns empty string for empty data', function () {
        $component = new ProductAvailability();
        $reflection = new ReflectionClass($component);
        $method = $reflection->getMethod('buildStockByCenterInfo');
        $method->setAccessible(true);

        $info = $method->invoke($component, []);

        expect($info)->toBe('');
    });
});
