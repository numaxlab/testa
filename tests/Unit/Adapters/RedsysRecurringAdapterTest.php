<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Testa\Models\Membership\Subscription;
use Testa\Payment\Adapters\RedsysRecurringAdapter;
use Testa\Payment\RecurringChargeResult;
use Testa\Payment\RedsysRecurringChargeData;
use Testa\Tests\TestCase;

uses(TestCase::class);

// ──────────────────────────────────────────────
// Helpers
// ──────────────────────────────────────────────

function makeMitSuccessResponse(): array
{
    $params = ['Ds_Response' => '0000', 'Ds_Amount' => '5000'];
    $encoded = base64_encode(json_encode($params));

    return [
        'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
        'Ds_MerchantParameters' => $encoded,
        'Ds_Signature' => 'fakesignature',
    ];
}

function makeMitRejectionResponse(string $code = '0190'): array
{
    $params = ['Ds_Response' => $code, 'Ds_Amount' => '5000'];
    $encoded = base64_encode(json_encode($params));

    return [
        'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
        'Ds_MerchantParameters' => $encoded,
        'Ds_Signature' => 'fakesignature',
    ];
}

function makeSubscriptionStub(): Subscription
{
    $sub = Mockery::mock(Subscription::class)->makePartial();
    $sub->id = 1;

    return $sub;
}

function configureRedsysRecurring(): void
{
    config([
        'testa.redsys_recurring.enabled' => true,
        'testa.redsys_recurring.merchant_code' => '999008881',
        'testa.redsys_recurring.secret_key' => 'sq7HjrUOBfKmC576ILgskD5srU870gJ7',
        'testa.redsys_recurring.terminal' => '001',
        'testa.redsys_recurring.endpoint' => 'https://sis-t.redsys.es:25443/sis/operaciones',
    ]);
}

// ──────────────────────────────────────────────
// Scenario: Safe abort when config missing
// ──────────────────────────────────────────────

it('aborts safely when redsys_recurring is disabled', function () {
    config(['testa.redsys_recurring.enabled' => false]);

    Http::fake(); // No HTTP call should be made

    $adapter = new RedsysRecurringAdapter();
    $data = new RedsysRecurringChargeData(
        subscription: makeSubscriptionStub(),
        paymentIdentifier: 'TOKEN123',
        configKey: 'redsys_recurring',
        amount: 5000,
    );

    $result = $adapter->charge($data);

    expect($result)->toBeInstanceOf(RecurringChargeResult::class);
    expect($result->aborted)->toBeTrue();
    expect($result->success)->toBeFalse();

    Http::assertNothingSent();
});

it('aborts safely when merchant_code is missing', function () {
    config([
        'testa.redsys_recurring.enabled' => true,
        'testa.redsys_recurring.merchant_code' => null,
        'testa.redsys_recurring.secret_key' => 'sq7HjrUOBfKmC576ILgskD5srU870gJ7',
    ]);

    Http::fake();

    $adapter = new RedsysRecurringAdapter();
    $data = new RedsysRecurringChargeData(
        subscription: makeSubscriptionStub(),
        paymentIdentifier: 'TOKEN123',
        configKey: 'redsys_recurring',
        amount: 5000,
    );

    $result = $adapter->charge($data);

    expect($result->aborted)->toBeTrue();
    Http::assertNothingSent();
});

// ──────────────────────────────────────────────
// Scenario: Successful MIT charge
// ──────────────────────────────────────────────

it('returns success when Redsys responds with Ds_Response 0000', function () {
    configureRedsysRecurring();

    Http::fake([
        '*' => Http::response(json_encode(makeMitSuccessResponse()), 200),
    ]);

    $adapter = new RedsysRecurringAdapter();
    $data = new RedsysRecurringChargeData(
        subscription: makeSubscriptionStub(),
        paymentIdentifier: 'TOKEN_VALID_001',
        configKey: 'redsys_recurring',
        amount: 5000,
    );

    $result = $adapter->charge($data);

    expect($result)->toBeInstanceOf(RecurringChargeResult::class);
    expect($result->success)->toBeTrue();
    expect($result->aborted)->toBeFalse();
    expect($result->errorMessage)->toBeNull();

    Http::assertSentCount(1);
});

// ──────────────────────────────────────────────
// Scenario: Redsys rejects the charge
// ──────────────────────────────────────────────

it('returns failure when Redsys responds with error code', function () {
    configureRedsysRecurring();

    Http::fake([
        '*' => Http::response(json_encode(makeMitRejectionResponse('0190')), 200),
    ]);

    $adapter = new RedsysRecurringAdapter();
    $data = new RedsysRecurringChargeData(
        subscription: makeSubscriptionStub(),
        paymentIdentifier: 'TOKEN_EXPIRED',
        configKey: 'redsys_recurring',
        amount: 5000,
    );

    $result = $adapter->charge($data);

    expect($result->success)->toBeFalse();
    expect($result->aborted)->toBeFalse();
    expect($result->errorMessage)->toContain('0190');

    Http::assertSentCount(1);
});

it('returns failure when Redsys HTTP call itself fails', function () {
    configureRedsysRecurring();

    Http::fake([
        '*' => Http::response('', 500),
    ]);

    $adapter = new RedsysRecurringAdapter();
    $data = new RedsysRecurringChargeData(
        subscription: makeSubscriptionStub(),
        paymentIdentifier: 'TOKEN_VALID_001',
        configKey: 'redsys_recurring',
        amount: 5000,
    );

    $result = $adapter->charge($data);

    expect($result->success)->toBeFalse();
    expect($result->aborted)->toBeFalse();
});

// ──────────────────────────────────────────────
// Scenario: buildOrderNumber format compliance
// ──────────────────────────────────────────────

// ──────────────────────────────────────────────
// Scenario: buildOrderNumber format compliance
// ──────────────────────────────────────────────

it('buildOrderNumber produces a string of 4–12 characters that starts with a digit', function () {
    $adapter = new RedsysRecurringAdapter();
    $method = new ReflectionMethod($adapter, 'buildOrderNumber');
    $method->setAccessible(true);

    $ids = [1, 9, 99, 999, 9999, 10000, 99999, 100000, 9999999, 10000001];

    foreach ($ids as $id) {
        $order = $method->invoke($adapter, $id);

        expect(strlen($order))->toBeGreaterThanOrEqual(4)
            ->toBeLessThanOrEqual(12);
        expect(ctype_digit($order[0]))->toBeTrue("ID {$id}: first char must be a digit, got '{$order}'");
        expect(ctype_alnum($order))->toBeTrue("ID {$id}: all chars must be alphanumeric, got '{$order}'");
    }
});

it('buildOrderNumber produces distinct values for distinct subscription IDs in the same hour', function () {
    $adapter = new RedsysRecurringAdapter();
    $method = new ReflectionMethod($adapter, 'buildOrderNumber');
    $method->setAccessible(true);

    $ids = [1, 2, 9999, 10000, 10001, 99999, 100000, 9999999];
    $seen = [];

    foreach ($ids as $id) {
        $order = $method->invoke($adapter, $id);
        $collision = $seen[$order] ?? null;
        expect($collision)->toBeNull("Collision: ID {$id} and ID {$collision} both produce '{$order}'");
        $seen[$order] = $id;
    }
});
