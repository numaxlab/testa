<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Address;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\Customer as LunarCustomer;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Models\Customer;
use Testa\Storefront\Data\AddressData;
use Testa\Storefront\UseCases\Account\UpdateAddress;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);

    $customer = Customer::find(LunarCustomer::factory()->create()->id);
    $this->country = Country::factory()->create();

    $this->address = Address::factory()->create([
        'customer_id' => $customer->id,
        'country_id' => $this->country->id,
        'first_name' => 'Old',
        'last_name' => 'Name',
        'shipping_default' => false,
    ]);
});

it('updates the address fields', function () {
    $data = new AddressData(
        first_name: 'New',
        last_name: 'Name',
        company_name: null,
        tax_identifier: null,
        country_id: $this->country->id,
        state: null,
        postcode: '28001',
        city: 'Madrid',
        line_one: 'Calle Nueva 1',
        line_two: null,
        shipping_default: true,
        billing_default: false,
    );

    (new UpdateAddress())->execute($this->address, $data);

    $this->address->refresh();
    expect($this->address->first_name)
        ->toBe('New')
        ->and($this->address->last_name)->toBe('Name')
        ->and($this->address->city)->toBe('Madrid')
        ->and($this->address->line_one)->toBe('Calle Nueva 1')
        ->and($this->address->shipping_default)->toBeTrue();
});

it('does not create a new address record', function () {
    $data = new AddressData(
        first_name: 'New',
        last_name: 'Name',
        company_name: null,
        tax_identifier: null,
        country_id: $this->country->id,
        state: null,
        postcode: '28001',
        city: 'Madrid',
        line_one: 'Calle Nueva 1',
        line_two: null,
        shipping_default: false,
        billing_default: false,
    );

    (new UpdateAddress())->execute($this->address, $data);

    expect(Address::count())->toBe(1);
});
