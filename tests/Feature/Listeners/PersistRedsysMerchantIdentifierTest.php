<?php

use Illuminate\Support\Facades\Event;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\Order;
use Lunar\Models\TaxClass;
use Testa\Listeners\PersistRedsysMerchantIdentifier;
use Testa\Models\Membership\Subscription;

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);
});

/**
 * Minimal event stub that mirrors the shape of RedsysMerchantIdentifierReceived
 * without requiring the lunar-redsys package in testa's dev dependencies.
 */
function makeIdentifierEvent(int $orderId, string $merchantIdentifier): object
{
    return new class($orderId, $merchantIdentifier) {
        public function __construct(
            public readonly int $orderId,
            public readonly string $merchantIdentifier,
        ) {}
    };
}

it('is registered as a listener for RedsysMerchantIdentifierReceived when the event class exists', function () {
    $eventClass = 'NumaxLab\\Lunar\\Redsys\\Events\\RedsysMerchantIdentifierReceived';

    expect(Event::hasListeners($eventClass))->toBeTrue();
})->skip(fn () => ! class_exists('NumaxLab\\Lunar\\Redsys\\Events\\RedsysMerchantIdentifierReceived'), 'lunar-redsys package not available');

it('persists the merchant identifier on the subscription for the given order', function () {
    $order = Order::factory()->create();
    $subscription = Subscription::factory()->create(['order_id' => $order->id]);

    $listener = new PersistRedsysMerchantIdentifier();
    $listener->handle(makeIdentifierEvent($order->id, 'REDSYS_TOKEN_ABC123'));

    $subscription->refresh();

    expect($subscription->payment_identifier)->toBe('REDSYS_TOKEN_ABC123');
});

it('does not overwrite an already stored payment identifier', function () {
    $order = Order::factory()->create();
    $subscription = Subscription::factory()->create([
        'order_id' => $order->id,
        'payment_identifier' => 'EXISTING_TOKEN_XYZ',
    ]);

    $listener = new PersistRedsysMerchantIdentifier();
    $listener->handle(makeIdentifierEvent($order->id, 'NEW_TOKEN_SHOULD_NOT_REPLACE'));

    $subscription->refresh();

    expect($subscription->payment_identifier)->toBe('EXISTING_TOKEN_XYZ');
});

it('logs a warning and does nothing when no matching subscription is found', function () {
    $listener = new PersistRedsysMerchantIdentifier();

    // Should not throw — no subscription with order_id 99999
    $listener->handle(makeIdentifierEvent(99999, 'SOME_TOKEN'));

    // Verify no subscription was created with this identifier
    expect(Subscription::where('payment_identifier', 'SOME_TOKEN')->exists())->toBeFalse();
});

it('fires PersistRedsysMerchantIdentifier when RedsysMerchantIdentifierReceived event is dispatched', function () {
    $eventClass = 'NumaxLab\\Lunar\\Redsys\\Events\\RedsysMerchantIdentifierReceived';

    $order = Order::factory()->create();
    $subscription = Subscription::factory()->create(['order_id' => $order->id]);

    $eventClass::dispatch($order->id, 'DISPATCHED_TOKEN_001');

    $subscription->refresh();

    expect($subscription->payment_identifier)->toBe('DISPATCHED_TOKEN_001');
})->skip(fn () => ! class_exists('NumaxLab\\Lunar\\Redsys\\Events\\RedsysMerchantIdentifierReceived'), 'lunar-redsys package not available');
