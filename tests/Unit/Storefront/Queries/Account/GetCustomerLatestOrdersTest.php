<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Customer as LunarCustomer;
use Lunar\Models\Language;
use Lunar\Models\Order;
use Lunar\Models\TaxClass;
use Testa\Models\Customer;
use Testa\Storefront\Queries\Account\GetCustomerLatestOrders;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);

    $this->customer = Customer::find(LunarCustomer::factory()->create()->id);
});

it('returns a collection', function () {
    $result = new GetCustomerLatestOrders()->execute($this->customer);

    expect($result)->toBeInstanceOf(Collection::class);
});

it('returns qualifying orders for the customer', function () {
    Order::factory()->create([
        'customer_id' => $this->customer->id,
        'status' => 'paid',
        'is_geslib' => true,
    ]);

    $result = new GetCustomerLatestOrders()->execute($this->customer);

    expect($result)->toHaveCount(1);
});

it('excludes orders with status awaiting-payment', function () {
    Order::factory()->create([
        'customer_id' => $this->customer->id,
        'status' => 'awaiting-payment',
        'is_geslib' => true,
    ]);

    $result = new GetCustomerLatestOrders()->execute($this->customer);

    expect($result)->toBeEmpty();
});

it('excludes orders with status cancelled', function () {
    Order::factory()->create([
        'customer_id' => $this->customer->id,
        'status' => 'cancelled',
        'is_geslib' => true,
    ]);

    $result = new GetCustomerLatestOrders()->execute($this->customer);

    expect($result)->toBeEmpty();
});

it('excludes orders where is_geslib is false', function () {
    Order::factory()->create([
        'customer_id' => $this->customer->id,
        'status' => 'paid',
        'is_geslib' => false,
    ]);

    $result = new GetCustomerLatestOrders()->execute($this->customer);

    expect($result)->toBeEmpty();
});

it('does not return orders from other customers', function () {
    $otherCustomer = Customer::find(LunarCustomer::factory()->create()->id);
    Order::factory()->create([
        'customer_id' => $otherCustomer->id,
        'status' => 'paid',
        'is_geslib' => true,
    ]);

    $result = new GetCustomerLatestOrders()->execute($this->customer);

    expect($result)->toBeEmpty();
});

it('limits results to the given limit', function () {
    Order::factory()->count(5)->create([
        'customer_id' => $this->customer->id,
        'status' => 'paid',
        'is_geslib' => true,
    ]);

    $result = new GetCustomerLatestOrders()->execute($this->customer, 2);

    expect($result)->toHaveCount(2);
});

it('defaults to a limit of 2', function () {
    Order::factory()->count(5)->create([
        'customer_id' => $this->customer->id,
        'status' => 'paid',
        'is_geslib' => true,
    ]);

    $result = new GetCustomerLatestOrders()->execute($this->customer);

    expect($result)->toHaveCount(2);
});
