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
use Testa\Storefront\UseCases\Account\DeleteCustomerAddress;
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
    ]);
});

it('deletes the address', function () {
    (new DeleteCustomerAddress())->execute($this->customer, $this->address);

    expect(Address::count())->toBe(0);
});

it('throws AuthorizationException when address belongs to another customer', function () {
    $otherCustomer = Customer::find(LunarCustomer::factory()->create()->id);

    (new DeleteCustomerAddress())->execute($otherCustomer, $this->address);
})->throws(AuthorizationException::class);

it('does not delete addresses belonging to other customers', function () {
    $otherCustomer = Customer::find(LunarCustomer::factory()->create()->id);
    Address::factory()->create([
        'customer_id' => $otherCustomer->id,
        'country_id' => $this->country->id,
    ]);

    (new DeleteCustomerAddress())->execute($this->customer, $this->address);

    expect(Address::count())->toBe(1);
});
