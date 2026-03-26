<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Order;
use Lunar\Models\TaxClass;
use Testa\Storefront\Queries\Checkout\GetOrderById;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create(['default' => true]);
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true, 'decimal_places' => 2]);
    Channel::factory()->create(['default' => true]);
    CustomerGroup::factory()->create(['default' => true]);
});

it('returns the order matching id and fingerprint', function () {
    $order = Order::factory()->create(['fingerprint' => 'abc123']);

    $result = new GetOrderById()->execute($order->id, 'abc123');

    expect($result->id)->toBe($order->id);
});

it('throws ModelNotFoundException when id does not match', function () {
    Order::factory()->create(['fingerprint' => 'abc123']);

    new GetOrderById()->execute(9999, 'abc123');
})->throws(ModelNotFoundException::class);

it('throws ModelNotFoundException when fingerprint does not match', function () {
    $order = Order::factory()->create(['fingerprint' => 'abc123']);

    new GetOrderById()->execute($order->id, 'wrong-fingerprint');
})->throws(ModelNotFoundException::class);

it('eager loads shippingAddress relation', function () {
    $order = Order::factory()->create(['fingerprint' => 'abc123']);

    $result = new GetOrderById()->execute($order->id, 'abc123');

    expect($result->relationLoaded('shippingAddress'))->toBeTrue();
});
