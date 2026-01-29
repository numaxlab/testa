<?php

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Language;
use Testa\Models\Membership\MembershipTier;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(fn () => Language::factory()->create());

it('has plans relationship', function () {
    $tier = new MembershipTier();
    expect($tier->plans())->toBeInstanceOf(HasMany::class);
});

it('has purchasable relationship', function () {
    $tier = new MembershipTier();
    expect($tier->purchasable())->toBeInstanceOf(BelongsTo::class);
});

it('has translatable name field', function () {
    $tier = new MembershipTier();
    expect($tier->translatable)->toContain('name');
});

it('has translatable description field', function () {
    $tier = new MembershipTier();
    expect($tier->translatable)->toContain('description');
});

it('can be created with factory', function () {
    $tier = MembershipTier::factory()->create();
    expect($tier)->toBeInstanceOf(MembershipTier::class)
        ->and($tier->exists)->toBeTrue();
});
