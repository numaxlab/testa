<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Testa\Contracts\Payment\PaymentContext;
use Testa\Contracts\Payment\PaymentGatewayAdapter;
use Testa\Contracts\Payment\PaymentResult;
use Testa\Payment\Adapters\OfflinePaymentAdapter;
use Testa\Payment\PaymentGatewayRegistry;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('registers and retrieves adapters by driver name', function () {
    $registry = new PaymentGatewayRegistry();
    $adapter = new OfflinePaymentAdapter();

    $registry->register($adapter);

    expect($registry->getAdapter('offline'))->toBe($adapter);
});

it('throws exception when adapter not found', function () {
    $registry = new PaymentGatewayRegistry();

    $registry->getAdapter('non-existent');
})->throws(InvalidArgumentException::class, 'No adapter registered for driver: non-existent');

it('checks if adapter is registered', function () {
    $registry = new PaymentGatewayRegistry();
    $adapter = new OfflinePaymentAdapter();

    expect($registry->hasAdapter('offline'))->toBeFalse();

    $registry->register($adapter);

    expect($registry->hasAdapter('offline'))->toBeTrue();
});

it('returns all registered driver names', function () {
    $registry = new PaymentGatewayRegistry();

    $customAdapter = new class implements PaymentGatewayAdapter {
        public function getDriverName(): string
        {
            return 'custom';
        }

        public function prepareAuthorizationData(PaymentContext $context): array
        {
            return [];
        }

        public function handleAuthorizationResponse(
            mixed $response,
            object $paymentDriver,
            PaymentContext $context,
        ): PaymentResult {
            return PaymentResult::success(1);
        }
    };

    $registry->register(new OfflinePaymentAdapter());
    $registry->register($customAdapter);

    expect($registry->getRegisteredDrivers())->toContain('offline', 'custom');
});

it('gets adapter for payment type based on lunar config', function () {
    $registry = new PaymentGatewayRegistry();
    $adapter = new OfflinePaymentAdapter();
    $registry->register($adapter);

    config(['lunar.payments.types.cash-on-delivery.driver' => 'offline']);

    expect($registry->getAdapterForPaymentType('cash-on-delivery'))->toBe($adapter);
});

it('throws exception when no driver configured for payment type', function () {
    $registry = new PaymentGatewayRegistry();

    $registry->getAdapterForPaymentType('unknown-type');
})->throws(InvalidArgumentException::class, 'No driver configured for payment type: unknown-type');

it('can register custom adapter implementing interface', function () {
    $customAdapter = new class implements PaymentGatewayAdapter {
        public function getDriverName(): string
        {
            return 'custom';
        }

        public function prepareAuthorizationData(PaymentContext $context): array
        {
            return ['custom' => 'data'];
        }

        public function handleAuthorizationResponse(
            mixed $response,
            object $paymentDriver,
            PaymentContext $context,
        ): PaymentResult {
            return PaymentResult::success(1);
        }
    };

    $registry = new PaymentGatewayRegistry();
    $registry->register($customAdapter);

    expect($registry->hasAdapter('custom'))->toBeTrue();
    expect($registry->getAdapter('custom'))->toBe($customAdapter);
});

it('allows method chaining when registering adapters', function () {
    $registry = new PaymentGatewayRegistry();

    $result = $registry
        ->register(new OfflinePaymentAdapter())
        ->register(new OfflinePaymentAdapter()); // register same adapter twice to test chaining

    expect($result)->toBe($registry);
});
