<?php

use Lunar\Models\Cart;
use Lunar\Models\Order;
use Testa\Contracts\Payment\PaymentContext;

it('creates context from constructor with all properties', function () {
    $cart = Mockery::mock(Cart::class);
    $order = Mockery::mock(Order::class);

    $context = new PaymentContext(
        paymentType: 'card',
        order: $order,
        cart: $cart,
        successRoute: 'https://example.com/success',
        failureRoute: 'https://example.com/failure',
        orderType: 'Pedido librería',
    );

    expect($context->paymentType)->toBe('card');
    expect($context->order)->toBe($order);
    expect($context->cart)->toBe($cart);
    expect($context->successRoute)->toBe('https://example.com/success');
    expect($context->failureRoute)->toBe('https://example.com/failure');
    expect($context->orderType)->toBe('Pedido librería');
});

it('allows null order type', function () {
    $cart = Mockery::mock(Cart::class);
    $order = Mockery::mock(Order::class);

    $context = new PaymentContext(
        paymentType: 'card',
        order: $order,
        cart: $cart,
        successRoute: 'https://example.com/success',
        failureRoute: 'https://example.com/failure',
    );

    expect($context->orderType)->toBeNull();
});

it('is immutable readonly class', function () {
    $cart = Mockery::mock(Cart::class);
    $order = Mockery::mock(Order::class);

    $context = new PaymentContext(
        paymentType: 'card',
        order: $order,
        cart: $cart,
        successRoute: 'https://example.com/success',
        failureRoute: 'https://example.com/failure',
    );

    $reflection = new ReflectionClass($context);
    expect($reflection->isReadOnly())->toBeTrue();
});
