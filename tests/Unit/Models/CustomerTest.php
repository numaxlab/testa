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
use Testa\Storefront\Queries\Membership\CustomerHasActiveBenefit;
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

it('credit eligibility is false when customer has no subscriptions', function () {
    $customer = Customer::find(LunarCustomer::factory()->create()->id);

    $result = new CustomerHasActiveBenefit()->execute($customer, Benefit::CREDIT_PAYMENT_TYPE);

    expect($result)->toBeFalse();
});

it('credit eligibility is false when subscription has no credit benefit', function () {
    $customer = Customer::find(LunarCustomer::factory()->create()->id);
    $plan = MembershipPlan::factory()->create();

    Subscription::factory()->active()->create([
        'customer_id' => $customer->id,
        'membership_plan_id' => $plan->id,
    ]);

    $result = new CustomerHasActiveBenefit()->execute($customer, Benefit::CREDIT_PAYMENT_TYPE);

    expect($result)->toBeFalse();
});

it('credit eligibility is false when subscription with credit benefit is expired', function () {
    $customer = Customer::find(LunarCustomer::factory()->create()->id);
    $benefit = Benefit::factory()->creditPaymentType()->create();
    $plan = MembershipPlan::factory()->hasAttached($benefit)->create();

    Subscription::factory()->expired()->create([
        'customer_id' => $customer->id,
        'membership_plan_id' => $plan->id,
    ]);

    $result = new CustomerHasActiveBenefit()->execute($customer, Benefit::CREDIT_PAYMENT_TYPE);

    expect($result)->toBeFalse();
});

it('credit eligibility is false when subscription with credit benefit is cancelled', function () {
    $customer = Customer::find(LunarCustomer::factory()->create()->id);
    $benefit = Benefit::factory()->creditPaymentType()->create();
    $plan = MembershipPlan::factory()->hasAttached($benefit)->create();

    Subscription::factory()->cancelled()->create([
        'customer_id' => $customer->id,
        'membership_plan_id' => $plan->id,
    ]);

    $result = new CustomerHasActiveBenefit()->execute($customer, Benefit::CREDIT_PAYMENT_TYPE);

    expect($result)->toBeFalse();
});

it('credit eligibility is true when active subscription includes credit benefit', function () {
    $customer = Customer::find(LunarCustomer::factory()->create()->id);
    $benefit = Benefit::factory()->creditPaymentType()->create();
    $plan = MembershipPlan::factory()->hasAttached($benefit)->create();

    Subscription::factory()->active()->create([
        'customer_id' => $customer->id,
        'membership_plan_id' => $plan->id,
    ]);

    $result = new CustomerHasActiveBenefit()->execute($customer, Benefit::CREDIT_PAYMENT_TYPE);

    expect($result)->toBeTrue();
});
