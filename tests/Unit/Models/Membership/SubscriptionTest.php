<?php

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Models\Membership\Subscription;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);
});

it('has customer relationship', function () {
    $subscription = new Subscription();
    expect($subscription->customer())->toBeInstanceOf(BelongsTo::class);
});

it('has plan relationship', function () {
    $subscription = new Subscription();
    expect($subscription->plan())->toBeInstanceOf(BelongsTo::class);
});

it('has order relationship', function () {
    $subscription = new Subscription();
    expect($subscription->order())->toBeInstanceOf(BelongsTo::class);
});

it('has status constants', function () {
    expect(Subscription::STATUS_ACTIVE)->toBe('active')
        ->and(Subscription::STATUS_CANCELLED)->toBe('cancelled')
        ->and(Subscription::STATUS_PENDING_PAYMENT)->toBe('pending_payment');
});

it('casts started_at to date', function () {
    $subscription = Subscription::factory()->create();
    expect($subscription->started_at)->toBeInstanceOf(Carbon::class);
});

it('casts expires_at to date', function () {
    $subscription = Subscription::factory()->create();
    expect($subscription->expires_at)->toBeInstanceOf(Carbon::class);
});

it('can be created with factory', function () {
    $subscription = Subscription::factory()->create();
    expect($subscription)->toBeInstanceOf(Subscription::class)
        ->and($subscription->exists)->toBeTrue();
});

it('can create active subscription with factory', function () {
    $subscription = Subscription::factory()->active()->create();
    expect($subscription->status)->toBe(Subscription::STATUS_ACTIVE)
        ->and($subscription->expires_at->isFuture())->toBeTrue();
});

it('can create cancelled subscription with factory', function () {
    $subscription = Subscription::factory()->cancelled()->create();
    expect($subscription->status)->toBe(Subscription::STATUS_CANCELLED);
});

it('can create expired subscription with factory', function () {
    $subscription = Subscription::factory()->expired()->create();
    expect($subscription->expires_at->isPast())->toBeTrue();
});

it('has nullable payment_identifier by default', function () {
    $subscription = Subscription::factory()->create();
    expect($subscription->payment_identifier)->toBeNull();
});

it('can persist a payment identifier token', function () {
    $subscription = Subscription::factory()->create();

    $subscription->setPaymentIdentifier('OPAQUE_TOKEN_ABC123');

    $subscription->refresh();

    expect($subscription->payment_identifier)->toBe('OPAQUE_TOKEN_ABC123');
});

it('reports hasPaymentIdentifier as false when no token stored', function () {
    $subscription = Subscription::factory()->create();
    expect($subscription->hasPaymentIdentifier())->toBeFalse();
});

it('reports hasPaymentIdentifier as true once token is stored', function () {
    $subscription = Subscription::factory()->create();
    $subscription->setPaymentIdentifier('OPAQUE_TOKEN_XYZ789');

    expect($subscription->hasPaymentIdentifier())->toBeTrue();
});
