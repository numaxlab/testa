<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Address;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\Customer as LunarCustomer;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Models\Customer;
use Testa\Storefront\Data\AddressData;
use Testa\Storefront\UseCases\Account\UpdateCustomerAddress;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);

    $this->customer = Customer::find(LunarCustomer::factory()->create()->id);
    $this->country = Country::factory()->create();

    $this->address = Address::factory()->create([
        'customer_id' => $this->customer->id,
        'country_id' => $this->country->id,
        'first_name' => 'Old',
        'last_name' => 'Name',
        'shipping_default' => false,
    ]);
});

function makeUpdateAddressData(int $countryId, array $overrides = []): AddressData
{
    return new AddressData(
        first_name: $overrides['first_name'] ?? 'New',
        last_name: $overrides['last_name'] ?? 'Name',
        company_name: null,
        tax_identifier: null,
        country_id: $countryId,
        state: null,
        postcode: $overrides['postcode'] ?? '28001',
        city: $overrides['city'] ?? 'Madrid',
        line_one: $overrides['line_one'] ?? 'Calle Nueva 1',
        line_two: null,
        shipping_default: $overrides['shipping_default'] ?? false,
        billing_default: false,
    );
}

it('updates the address fields', function () {
    $data = makeUpdateAddressData($this->country->id, ['shipping_default' => true]);

    (new UpdateCustomerAddress())->execute($this->customer, $this->address, $data);

    $this->address->refresh();
    expect($this->address->first_name)
        ->toBe('New')
        ->and($this->address->city)->toBe('Madrid')
        ->and($this->address->line_one)->toBe('Calle Nueva 1')
        ->and($this->address->shipping_default)->toBeTrue();
});

it('does not create a new address record', function () {
    $data = makeUpdateAddressData($this->country->id);

    (new UpdateCustomerAddress())->execute($this->customer, $this->address, $data);

    expect(Address::count())->toBe(1);
});

it('throws AuthorizationException when address belongs to another customer', function () {
    $otherCustomer = Customer::find(LunarCustomer::factory()->create()->id);
    $data = makeUpdateAddressData($this->country->id);

    (new UpdateCustomerAddress())->execute($otherCustomer, $this->address, $data);
})->throws(AuthorizationException::class);
