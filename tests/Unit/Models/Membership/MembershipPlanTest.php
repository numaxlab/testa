<?php

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Models\Membership\MembershipPlan;
use Testa\Models\Membership\MembershipTier;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);
});

it('has tier relationship', function () {
    $plan = new MembershipPlan();
    expect($plan->tier())->toBeInstanceOf(BelongsTo::class);
});

it('has benefits relationship', function () {
    $plan = new MembershipPlan();
    expect($plan->benefits())->toBeInstanceOf(BelongsToMany::class);
});

it('has taxClass relationship', function () {
    $plan = new MembershipPlan();
    expect($plan->taxClass())->toBeInstanceOf(BelongsTo::class);
});

it('has variant relationship', function () {
    $plan = new MembershipPlan();
    expect($plan->variant())->toBeInstanceOf(BelongsTo::class);
});

it('has billing interval constants', function () {
    expect(MembershipPlan::BILLING_INTERVAL_MONTHLY)->toBe('monthly')
        ->and(MembershipPlan::BILLING_INTERVAL_BIMONTHLY)->toBe('bimonthly')
        ->and(MembershipPlan::BILLING_INTERVAL_QUARTERLY)->toBe('quarterly')
        ->and(MembershipPlan::BILLING_INTERVAL_YEARLY)->toBe('yearly');
});

it('has translatable name field', function () {
    $plan = new MembershipPlan();
    expect($plan->translatable)->toContain('name');
});

it('has translatable description field', function () {
    $plan = new MembershipPlan();
    expect($plan->translatable)->toContain('description');
});

it('returns period for billing interval', function (string $interval, string $expected) {
    $plan = new MembershipPlan(['billing_interval' => $interval]);
    expect($plan->period())->toBe($expected);
})->with([
    [MembershipPlan::BILLING_INTERVAL_MONTHLY, 'mes'],
    [MembershipPlan::BILLING_INTERVAL_BIMONTHLY, '2 meses'],
    [MembershipPlan::BILLING_INTERVAL_QUARTERLY, 'trimestre'],
    [MembershipPlan::BILLING_INTERVAL_YEARLY, 'año'],
]);

it('returns default period for unknown billing interval', function () {
    $plan = new MembershipPlan(['billing_interval' => 'unknown']);
    expect($plan->period())->toBe('mes');
});

it('returns full name combining tier and plan name', function () {
    $tier = MembershipTier::factory()->create(['name' => 'Gold']);
    $plan = MembershipPlan::factory()->create([
        'membership_tier_id' => $tier->id,
        'name' => 'Annual',
    ]);

    expect($plan->full_name)->toBe('Gold - Annual');
});

it('can be created with factory', function () {
    $plan = MembershipPlan::factory()->create();
    expect($plan)->toBeInstanceOf(MembershipPlan::class)
        ->and($plan->exists)->toBeTrue();
});

it('can create monthly plan with factory', function () {
    $plan = MembershipPlan::factory()->monthly()->create();
    expect($plan->billing_interval)->toBe(MembershipPlan::BILLING_INTERVAL_MONTHLY);
});

it('can create quarterly plan with factory', function () {
    $plan = MembershipPlan::factory()->quarterly()->create();
    expect($plan->billing_interval)->toBe(MembershipPlan::BILLING_INTERVAL_QUARTERLY);
});

it('can create yearly plan with factory', function () {
    $plan = MembershipPlan::factory()->yearly()->create();
    expect($plan->billing_interval)->toBe(MembershipPlan::BILLING_INTERVAL_YEARLY);
});
