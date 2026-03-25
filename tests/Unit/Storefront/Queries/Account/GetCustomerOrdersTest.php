<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Lunar\Models\Currency;
use Lunar\Models\Customer as LunarCustomer;
use Lunar\Models\Language;
use Lunar\Models\Order;
use Lunar\Models\TaxClass;
use Testa\Models\Customer;
use Testa\Storefront\Queries\Account\GetCustomerOrders;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);

    $this->customer = Customer::find(LunarCustomer::factory()->create()->id);
});

it('returns a paginator', function () {
    $result = new GetCustomerOrders()->execute($this->customer);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
});

it('excludes orders with status awaiting-payment', function () {
    Order::factory()->create([
        'customer_id' => $this->customer->id,
        'status' => 'awaiting-payment',
        'is_geslib' => true,
    ]);

    $result = new GetCustomerOrders()->execute($this->customer);

    expect($result->total())->toBe(0);
});

it('excludes orders with status cancelled', function () {
    Order::factory()->create([
        'customer_id' => $this->customer->id,
        'status' => 'cancelled',
        'is_geslib' => true,
    ]);

    $result = new GetCustomerOrders()->execute($this->customer);

    expect($result->total())->toBe(0);
});

it('excludes orders where is_geslib is false', function () {
    Order::factory()->create([
        'customer_id' => $this->customer->id,
        'status' => 'paid',
        'is_geslib' => false,
    ]);

    $result = new GetCustomerOrders()->execute($this->customer);

    expect($result->total())->toBe(0);
});

it('does not return orders from other customers', function () {
    $otherCustomer = Customer::find(LunarCustomer::factory()->create()->id);
    Order::factory()->create([
        'customer_id' => $otherCustomer->id,
        'status' => 'paid',
        'is_geslib' => true,
    ]);

    $result = new GetCustomerOrders()->execute($this->customer);

    expect($result->total())->toBe(0);
});

it('paginates with the given perPage value', function () {
    Order::factory()->count(10)->create([
        'customer_id' => $this->customer->id,
        'status' => 'paid',
        'is_geslib' => true,
    ]);

    $result = new GetCustomerOrders()->execute($this->customer, 3);

    expect($result->perPage())
        ->toBe(3)
        ->and($result->total())->toBe(10);
});

it('defaults to 8 items per page', function () {
    $result = new GetCustomerOrders()->execute($this->customer);

    expect($result->perPage())->toBe(8);
});
