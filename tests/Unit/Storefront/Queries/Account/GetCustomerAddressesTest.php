<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Address;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\Customer as LunarCustomer;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Models\Customer;
use Testa\Storefront\Queries\Account\GetCustomerAddresses;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);

    $this->customer = Customer::find(LunarCustomer::factory()->create()->id);
    $this->country = Country::factory()->create();
});

it('returns all addresses for the customer', function () {
    Address::factory()->count(3)->create([
        'customer_id' => $this->customer->id,
        'country_id' => $this->country->id,
    ]);

    $result = (new GetCustomerAddresses())->execute($this->customer);

    expect($result)->toHaveCount(3);
});

it('does not return addresses belonging to other customers', function () {
    $otherCustomer = Customer::find(LunarCustomer::factory()->create()->id);
    Address::factory()->create([
        'customer_id' => $this->customer->id,
        'country_id' => $this->country->id,
    ]);
    Address::factory()->create([
        'customer_id' => $otherCustomer->id,
        'country_id' => $this->country->id,
    ]);

    $result = (new GetCustomerAddresses())->execute($this->customer);

    expect($result)->toHaveCount(1);
});

it('returns an empty collection when customer has no addresses', function () {
    $result = (new GetCustomerAddresses())->execute($this->customer);

    expect($result)->toBeEmpty();
});
