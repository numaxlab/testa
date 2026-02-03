<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Cart;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\Order;
use Testa\Contracts\Payment\PaymentContext;
use Testa\Contracts\Payment\PaymentResultType;
use Testa\Payment\Adapters\OfflinePaymentAdapter;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    Currency::factory()->create();
});

it('returns correct driver name', function () {
    $adapter = new OfflinePaymentAdapter();

    expect($adapter->getDriverName())->toBe('offline');
});

it('prepares empty authorization data', function () {
    $adapter = new OfflinePaymentAdapter();

    $cart = Cart::factory()->create();
    $order = Order::factory()->create();

    $context = new PaymentContext(
        paymentType: 'cash-on-delivery',
        order: $order,
        cart: $cart,
        successRoute: 'https://example.com/success',
        failureRoute: 'https://example.com/failure',
    );

    expect($adapter->prepareAuthorizationData($context))->toBe([]);
});

it('handles successful authorization response', function () {
    $adapter = new OfflinePaymentAdapter();

    $cart = Cart::factory()->create();
    $order = Order::factory()->create();

    $context = new PaymentContext(
        paymentType: 'cash-on-delivery',
        order: $order,
        cart: $cart,
        successRoute: 'https://example.com/success',
        failureRoute: 'https://example.com/failure',
    );

    $response = (object) ['success' => true, 'orderId' => $order->id];
    $paymentDriver = new stdClass();

    $result = $adapter->handleAuthorizationResponse($response, $paymentDriver, $context);

    expect($result->type)->toBe(PaymentResultType::Success);
    expect($result->orderId)->toBe($order->id);
});

it('handles failed authorization response', function () {
    $adapter = new OfflinePaymentAdapter();

    $cart = Cart::factory()->create();
    $order = Order::factory()->create();

    $context = new PaymentContext(
        paymentType: 'cash-on-delivery',
        order: $order,
        cart: $cart,
        successRoute: 'https://example.com/success',
        failureRoute: 'https://example.com/failure',
    );

    $response = (object) ['success' => false];
    $paymentDriver = new stdClass();

    $result = $adapter->handleAuthorizationResponse($response, $paymentDriver, $context);

    expect($result->type)->toBe(PaymentResultType::Failure);
});
