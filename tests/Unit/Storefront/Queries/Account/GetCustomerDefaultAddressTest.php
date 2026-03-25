<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Address;
use Lunar\Models\Currency;
use Lunar\Models\Customer as LunarCustomer;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Models\Customer;
use Testa\Storefront\Queries\Account\GetCustomerDefaultAddress;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);

    $this->customer = Customer::find(LunarCustomer::factory()->create()->id);
});

it('returns null when the customer has no addresses', function () {
    $result = (new GetCustomerDefaultAddress())->execute($this->customer);

    expect($result)->toBeNull();
});

it('returns null when no address has shipping_default set to true', function () {
    Address::factory()->create([
        'customer_id' => $this->customer->id,
        'shipping_default' => false,
    ]);

    $result = (new GetCustomerDefaultAddress())->execute($this->customer);

    expect($result)->toBeNull();
});

it('returns the address with shipping_default true', function () {
    $default = Address::factory()->create([
        'customer_id' => $this->customer->id,
        'shipping_default' => true,
    ]);

    $result = (new GetCustomerDefaultAddress())->execute($this->customer);

    expect($result)
        ->toBeInstanceOf(Address::class)
        ->and($result->id)->toBe($default->id);
});

it('does not return a default address belonging to another customer', function () {
    $otherCustomer = Customer::find(LunarCustomer::factory()->create()->id);
    Address::factory()->create([
        'customer_id' => $otherCustomer->id,
        'shipping_default' => true,
    ]);

    $result = (new GetCustomerDefaultAddress())->execute($this->customer);

    expect($result)->toBeNull();
});

it('returns one address when multiple have shipping_default true', function () {
    $addresses = Address::factory()->count(2)->create([
        'customer_id' => $this->customer->id,
        'shipping_default' => true,
    ]);

    $result = (new GetCustomerDefaultAddress())->execute($this->customer);

    expect($result)
        ->toBeInstanceOf(Address::class)
        ->and($addresses->pluck('id'))->toContain($result->id);
});
