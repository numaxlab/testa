<?php

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Language;
use Testa\Models\Membership\Benefit;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(fn () => Language::factory()->create());

it('has membershipPlans relationship', function () {
    $benefit = new Benefit();
    expect($benefit->membershipPlans())->toBeInstanceOf(BelongsToMany::class);
});

it('has customerGroup relationship', function () {
    $benefit = new Benefit();
    expect($benefit->customerGroup())->toBeInstanceOf(BelongsTo::class);
});

it('has credit payment type constant', function () {
    expect(Benefit::CREDIT_PAYMENT_TYPE)->toBe('credit_payment_type');
});

it('has customer group constant', function () {
    expect(Benefit::CUSTOMER_GROUP)->toBe('customer_group');
});

it('has translatable name field', function () {
    $benefit = new Benefit();
    expect($benefit->translatable)->toContain('name');
});

it('can be created with factory', function () {
    $benefit = Benefit::factory()->create();
    expect($benefit)->toBeInstanceOf(Benefit::class)
        ->and($benefit->exists)->toBeTrue();
});

it('can create credit payment type benefit with factory', function () {
    $benefit = Benefit::factory()->creditPaymentType()->create();
    expect($benefit->code)->toBe(Benefit::CREDIT_PAYMENT_TYPE);
});

it('can create customer group benefit with factory', function () {
    $benefit = Benefit::factory()->customerGroup()->create();
    expect($benefit->code)->toBe(Benefit::CUSTOMER_GROUP);
});
