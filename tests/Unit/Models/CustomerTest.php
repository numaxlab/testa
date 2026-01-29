<?php

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Customer as LunarCustomer;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Models\Customer;
use Testa\Models\Membership\Benefit;
use Testa\Models\Membership\MembershipPlan;
use Testa\Models\Membership\Subscription;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);
});

it('extends Lunar Customer', function () {
    $customer = new Customer();
    expect($customer)->toBeInstanceOf(LunarCustomer::class);
});

it('has subscriptions relationship', function () {
    $customer = new Customer();
    expect($customer->subscriptions())->toBeInstanceOf(HasMany::class);
});

it('has activeSubscriptions relationship', function () {
    $customer = new Customer();
    expect($customer->activeSubscriptions())->toBeInstanceOf(HasMany::class);
});

it('has courses relationship', function () {
    $customer = new Customer();
    expect($customer->courses())->toBeInstanceOf(BelongsToMany::class);
});

it('cannot buy on credit when no subscriptions', function () {
    $customer = LunarCustomer::factory()->create();
    $testaCustomer = Customer::find($customer->id);

    expect($testaCustomer->canBuyOnCredit())->toBeFalse();
});

it('cannot buy on credit when subscription has no credit benefit', function () {
    $customer = LunarCustomer::factory()->create();
    $plan = MembershipPlan::factory()->create();

    Subscription::factory()->create([
        'customer_id' => $customer->id,
        'membership_plan_id' => $plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'expires_at' => now()->addYear(),
    ]);

    $testaCustomer = Customer::find($customer->id);
    expect($testaCustomer->canBuyOnCredit())->toBeFalse();
});

it('cannot buy on credit when subscription is expired', function () {
    $customer = LunarCustomer::factory()->create();
    $benefit = Benefit::factory()->creditPaymentType()->create();
    $plan = MembershipPlan::factory()->create();
    $plan->benefits()->attach($benefit);

    Subscription::factory()->create([
        'customer_id' => $customer->id,
        'membership_plan_id' => $plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'expires_at' => now()->subDay(),
    ]);

    $testaCustomer = Customer::find($customer->id);
    expect($testaCustomer->canBuyOnCredit())->toBeFalse();
});

it('cannot buy on credit when subscription is cancelled', function () {
    $customer = LunarCustomer::factory()->create();
    $benefit = Benefit::factory()->creditPaymentType()->create();
    $plan = MembershipPlan::factory()->create();
    $plan->benefits()->attach($benefit);

    Subscription::factory()->create([
        'customer_id' => $customer->id,
        'membership_plan_id' => $plan->id,
        'status' => Subscription::STATUS_CANCELLED,
        'expires_at' => now()->addYear(),
    ]);

    $testaCustomer = Customer::find($customer->id);
    expect($testaCustomer->canBuyOnCredit())->toBeFalse();
});

it('can buy on credit when active subscription has credit benefit', function () {
    $customer = LunarCustomer::factory()->create();
    $benefit = Benefit::factory()->creditPaymentType()->create();
    $plan = MembershipPlan::factory()->create();
    $plan->benefits()->attach($benefit);

    Subscription::factory()->create([
        'customer_id' => $customer->id,
        'membership_plan_id' => $plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'expires_at' => now()->addYear(),
    ]);

    $testaCustomer = Customer::find($customer->id);
    expect($testaCustomer->canBuyOnCredit())->toBeTrue();
});
