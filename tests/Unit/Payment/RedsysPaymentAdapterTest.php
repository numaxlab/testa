<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Cart;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\Order;
use Testa\Contracts\Payment\PaymentContext;
use Testa\Contracts\Payment\PaymentResultType;
use Testa\Payment\Adapters\RedsysPaymentAdapter;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    Currency::factory()->create();
});

it('returns correct driver name', function () {
    $adapter = new RedsysPaymentAdapter();

    expect($adapter->getDriverName())->toBe('redsys');
});

it('uses default config key when not specified', function () {
    $adapter = new RedsysPaymentAdapter();

    $cart = Cart::factory()->create();
    $order = Order::factory()->create();

    $context = new PaymentContext(
        paymentType: 'card',
        order: $order,
        cart: $cart,
        successRoute: 'https://example.com/success',
        failureRoute: 'https://example.com/failure',
    );

    config(['app.name' => 'Test Store']);
    $data = $adapter->prepareAuthorizationData($context);

    expect($data['config_key'])->toBe('default');
});

it('uses custom config key when specified', function () {
    $adapter = new RedsysPaymentAdapter(configKey: 'custom');

    $cart = Cart::factory()->create();
    $order = Order::factory()->create();

    $context = new PaymentContext(
        paymentType: 'card',
        order: $order,
        cart: $cart,
        successRoute: 'https://example.com/success',
        failureRoute: 'https://example.com/failure',
    );

    config(['app.name' => 'Test Store']);
    $data = $adapter->prepareAuthorizationData($context);

    expect($data['config_key'])->toBe('custom');
});

it('prepares authorization data with success and failure routes', function () {
    $adapter = new RedsysPaymentAdapter();

    $cart = Cart::factory()->create();
    $order = Order::factory()->create();

    $context = new PaymentContext(
        paymentType: 'card',
        order: $order,
        cart: $cart,
        successRoute: 'https://example.com/success',
        failureRoute: 'https://example.com/failure',
    );

    config(['app.name' => 'Test Store']);
    $data = $adapter->prepareAuthorizationData($context);

    expect($data['url_ok'])->toBe('https://example.com/success');
    expect($data['url_ko'])->toBe('https://example.com/failure');
    expect($data['product_description'])->toBe('Compra online en Test Store');
});

it('uses card method for card payment type', function () {
    $adapter = new RedsysPaymentAdapter();

    $cart = Cart::factory()->create();
    $order = Order::factory()->create();

    $context = new PaymentContext(
        paymentType: 'card',
        order: $order,
        cart: $cart,
        successRoute: 'https://example.com/success',
        failureRoute: 'https://example.com/failure',
    );

    config(['app.name' => 'Test Store']);
    $data = $adapter->prepareAuthorizationData($context);

    expect($data['method'])->toBe('C');
});

it('uses bizum method for bizum payment type', function () {
    $adapter = new RedsysPaymentAdapter();

    $cart = Cart::factory()->create();
    $order = Order::factory()->create();

    $context = new PaymentContext(
        paymentType: 'bizum',
        order: $order,
        cart: $cart,
        successRoute: 'https://example.com/success',
        failureRoute: 'https://example.com/failure',
    );

    config(['app.name' => 'Test Store']);
    $data = $adapter->prepareAuthorizationData($context);

    expect($data['method'])->toBe('z');
});

it('handles redirect response when response is RedirectToPaymentGateway', function () {
    // Skip if the Redsys package is not installed
    if (! class_exists(\NumaxLab\Lunar\Redsys\Responses\RedirectToPaymentGateway::class)) {
        $this->markTestSkipped('Redsys package not installed');
    }

    $adapter = new RedsysPaymentAdapter();

    $cart = Cart::factory()->create();
    $order = Order::factory()->create();

    $context = new PaymentContext(
        paymentType: 'card',
        order: $order,
        cart: $cart,
        successRoute: 'https://example.com/success',
        failureRoute: 'https://example.com/failure',
    );

    $response = new \NumaxLab\Lunar\Redsys\Responses\RedirectToPaymentGateway(
        success: true,
        orderId: $order->id,
    );
    $paymentDriver = new stdClass();

    $result = $adapter->handleAuthorizationResponse($response, $paymentDriver, $context);

    expect($result->type)->toBe(PaymentResultType::Redirect);
    expect($result->paymentDriver)->toBe($paymentDriver);
});

it('handles successful direct response', function () {
    $adapter = new RedsysPaymentAdapter();

    $cart = Cart::factory()->create();
    $order = Order::factory()->create();

    $context = new PaymentContext(
        paymentType: 'card',
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

it('handles failed response', function () {
    $adapter = new RedsysPaymentAdapter();

    $cart = Cart::factory()->create();
    $order = Order::factory()->create();

    $context = new PaymentContext(
        paymentType: 'card',
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
