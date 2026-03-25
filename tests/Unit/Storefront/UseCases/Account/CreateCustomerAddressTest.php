<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\Customer as LunarCustomer;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Models\Customer;
use Testa\Storefront\Data\AddressData;
use Testa\Storefront\UseCases\Account\CreateCustomerAddress;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);

    $this->customer = Customer::find(LunarCustomer::factory()->create()->id);
    $this->country = Country::factory()->create();
});

function makeAddressData(int $countryId, array $overrides = []): AddressData
{
    return new AddressData(
        first_name: $overrides['first_name'] ?? 'Jane',
        last_name: $overrides['last_name'] ?? 'Doe',
        company_name: $overrides['company_name'] ?? null,
        tax_identifier: $overrides['tax_identifier'] ?? null,
        country_id: $countryId,
        state: $overrides['state'] ?? null,
        postcode: $overrides['postcode'] ?? '28001',
        city: $overrides['city'] ?? 'Madrid',
        line_one: $overrides['line_one'] ?? 'Calle Mayor 1',
        line_two: $overrides['line_two'] ?? null,
        shipping_default: $overrides['shipping_default'] ?? false,
        billing_default: $overrides['billing_default'] ?? false,
    );
}

it('creates an address for the customer', function () {
    $data = makeAddressData($this->country->id);

    (new CreateCustomerAddress())->execute($this->customer, $data);

    expect($this->customer->addresses()->count())->toBe(1);
});

it('stores the correct field values', function () {
    $data = makeAddressData($this->country->id, [
        'first_name' => 'María',
        'last_name' => 'García',
        'postcode' => '08001',
        'city' => 'Barcelona',
        'line_one' => 'Passeig de Gràcia 92',
        'shipping_default' => true,
    ]);

    (new CreateCustomerAddress())->execute($this->customer, $data);

    $address = $this->customer->addresses()->first();
    expect($address->first_name)
        ->toBe('María')
        ->and($address->last_name)->toBe('García')
        ->and($address->postcode)->toBe('08001')
        ->and($address->city)->toBe('Barcelona')
        ->and($address->line_one)->toBe('Passeig de Gràcia 92')
        ->and($address->shipping_default)->toBeTrue();
});

it('does not create addresses for other customers', function () {
    $otherCustomer = Customer::find(LunarCustomer::factory()->create()->id);
    $data = makeAddressData($this->country->id);

    (new CreateCustomerAddress())->execute($this->customer, $data);

    expect($otherCustomer->addresses()->count())->toBe(0);
});
