<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Customer as LunarCustomer;
use Lunar\Models\Language;
use Lunar\Models\Order;
use Lunar\Models\TaxClass;
use Testa\Models\Customer;
use Testa\Storefront\Queries\Account\GetCustomerOrder;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);

    $this->customer = Customer::find(LunarCustomer::factory()->create()->id);
});

it('returns the order when reference matches and status is valid', function () {
    $order = Order::factory()->create([
        'customer_id' => $this->customer->id,
        'reference' => 'REF-001',
        'status' => 'paid',
    ]);

    $result = (new GetCustomerOrder())->execute($this->customer, 'REF-001');

    expect($result->id)->toBe($order->id);
});

it('throws ModelNotFoundException when reference does not exist', function () {
    (new GetCustomerOrder())->execute($this->customer, 'NONEXISTENT');
})->throws(ModelNotFoundException::class);

it('throws ModelNotFoundException when status is awaiting-payment', function () {
    Order::factory()->create([
        'customer_id' => $this->customer->id,
        'reference' => 'REF-002',
        'status' => 'awaiting-payment',
    ]);

    (new GetCustomerOrder())->execute($this->customer, 'REF-002');
})->throws(ModelNotFoundException::class);

it('throws ModelNotFoundException when status is cancelled', function () {
    Order::factory()->create([
        'customer_id' => $this->customer->id,
        'reference' => 'REF-003',
        'status' => 'cancelled',
    ]);

    (new GetCustomerOrder())->execute($this->customer, 'REF-003');
})->throws(ModelNotFoundException::class);

it('does not return orders from other customers', function () {
    $otherCustomer = Customer::find(LunarCustomer::factory()->create()->id);
    Order::factory()->create([
        'customer_id' => $otherCustomer->id,
        'reference' => 'REF-004',
        'status' => 'paid',
    ]);

    (new GetCustomerOrder())->execute($this->customer, 'REF-004');
})->throws(ModelNotFoundException::class);
