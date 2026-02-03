<?php

use Testa\Contracts\Payment\PaymentResult;
use Testa\Contracts\Payment\PaymentResultType;

it('creates success result', function () {
    $result = PaymentResult::success(orderId: 123);

    expect($result->type)->toBe(PaymentResultType::Success);
    expect($result->orderId)->toBe(123);
    expect($result->isSuccess())->toBeTrue();
    expect($result->isRedirect())->toBeFalse();
    expect($result->isFailure())->toBeFalse();
    expect($result->isPending())->toBeFalse();
});

it('creates redirect result', function () {
    $driver = new stdClass();
    $result = PaymentResult::redirect($driver);

    expect($result->type)->toBe(PaymentResultType::Redirect);
    expect($result->paymentDriver)->toBe($driver);
    expect($result->isRedirect())->toBeTrue();
    expect($result->isSuccess())->toBeFalse();
});

it('creates failure result', function () {
    $result = PaymentResult::failure('Payment declined');

    expect($result->type)->toBe(PaymentResultType::Failure);
    expect($result->errorMessage)->toBe('Payment declined');
    expect($result->isFailure())->toBeTrue();
    expect($result->isSuccess())->toBeFalse();
});

it('creates failure result without message', function () {
    $result = PaymentResult::failure();

    expect($result->type)->toBe(PaymentResultType::Failure);
    expect($result->errorMessage)->toBeNull();
});

it('creates pending result', function () {
    $result = PaymentResult::pending(orderId: 456);

    expect($result->type)->toBe(PaymentResultType::Pending);
    expect($result->orderId)->toBe(456);
    expect($result->isPending())->toBeTrue();
    expect($result->isSuccess())->toBeFalse();
});

it('is immutable readonly class', function () {
    $result = PaymentResult::success(123);

    $reflection = new ReflectionClass($result);
    expect($reflection->isReadOnly())->toBeTrue();
});
