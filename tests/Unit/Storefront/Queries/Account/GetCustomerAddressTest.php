<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Address;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\Customer as LunarCustomer;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Models\Customer;
use Testa\Storefront\Queries\Account\GetCustomerAddress;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);

    $this->customer = Customer::find(LunarCustomer::factory()->create()->id);
    $this->country = Country::factory()->create();
});

it('returns the address when it belongs to the customer', function () {
    $address = Address::factory()->create([
        'customer_id' => $this->customer->id,
        'country_id' => $this->country->id,
    ]);

    $result = (new GetCustomerAddress())->execute($this->customer, $address->id);

    expect($result->id)->toBe($address->id);
});

it('throws ModelNotFoundException when the address does not exist', function () {
    (new GetCustomerAddress())->execute($this->customer, 999);
})->throws(ModelNotFoundException::class);

it('throws ModelNotFoundException when the address belongs to another customer', function () {
    $otherCustomer = Customer::find(LunarCustomer::factory()->create()->id);
    $address = Address::factory()->create([
        'customer_id' => $otherCustomer->id,
        'country_id' => $this->country->id,
    ]);

    (new GetCustomerAddress())->execute($this->customer, $address->id);
})->throws(ModelNotFoundException::class);
