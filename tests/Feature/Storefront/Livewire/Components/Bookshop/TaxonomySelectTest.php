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
use Testa\Storefront\Livewire\Components\Bookshop\TaxonomySelect;

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

describe('TaxonomySelect component', function () {
    it('has search property defaulting to empty string', function () {
        $component = new TaxonomySelect();

        expect(property_exists($component, 'search'))->toBeTrue();
        expect($component->search)->toBe('');
    });

    it('has selectedId property defaulting to null', function () {
        $component = new TaxonomySelect();

        expect(property_exists($component, 'selectedId'))->toBeTrue();
        expect($component->selectedId)->toBeNull();
    });

    it('has selectedName property with default value', function () {
        $component = new TaxonomySelect();

        expect(property_exists($component, 'selectedName'))->toBeTrue();
        expect($component->selectedName)->toBe('Selecciona una materia');
    });

    it('has isOpen property defaulting to false', function () {
        $component = new TaxonomySelect();

        expect(property_exists($component, 'isOpen'))->toBeTrue();
        expect($component->isOpen)->toBeFalse();
    });

    it('returns correct view name', function () {
        $viewPath = 'testa::storefront.livewire.components.bookshop.taxonomy-select';

        expect(view()->exists($viewPath))->toBeTrue();
    });

    it('has selectOption method', function () {
        $component = new TaxonomySelect();

        expect(method_exists($component, 'selectOption'))->toBeTrue();
    });

    it('has clearSelection method', function () {
        $component = new TaxonomySelect();

        expect(method_exists($component, 'clearSelection'))->toBeTrue();
    });
});

describe('TaxonomySelect selectOption method', function () {
    it('selectOption sets selectedId', function () {
        $component = new TaxonomySelect();
        $component->selectOption(123, 'Test Category');

        expect($component->selectedId)->toBe(123);
    });

    it('selectOption sets selectedName', function () {
        $component = new TaxonomySelect();
        $component->selectOption(123, 'Test Category');

        expect($component->selectedName)->toBe('Test Category');
    });

    it('selectOption closes dropdown', function () {
        $component = new TaxonomySelect();
        $component->isOpen = true;
        $component->selectOption(123, 'Test Category');

        expect($component->isOpen)->toBeFalse();
    });

    it('selectOption clears search', function () {
        $component = new TaxonomySelect();
        $component->search = 'test search';
        $component->selectOption(123, 'Test Category');

        expect($component->search)->toBe('');
    });
});

describe('TaxonomySelect clearSelection method', function () {
    it('clearSelection resets selectedId to null', function () {
        $component = new TaxonomySelect();
        $component->selectedId = 123;
        $component->clearSelection();

        expect($component->selectedId)->toBeNull();
    });

    it('clearSelection resets selectedName to default', function () {
        $component = new TaxonomySelect();
        $component->selectedName = 'Test Category';
        $component->clearSelection();

        expect($component->selectedName)->toBe('Selecciona una materia');
    });

    it('clearSelection closes dropdown', function () {
        $component = new TaxonomySelect();
        $component->isOpen = true;
        $component->clearSelection();

        expect($component->isOpen)->toBeFalse();
    });

    it('clearSelection clears search', function () {
        $component = new TaxonomySelect();
        $component->search = 'test search';
        $component->clearSelection();

        expect($component->search)->toBe('');
    });
});
