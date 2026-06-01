<?php

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

    $this->customer = Customer::find(LunarCustomer::factory()->create()->id);
    $this->benefit = Benefit::factory()->create(['code' => Benefit::CEMA_COURSE_ACCESS]);
    $this->plan = MembershipPlan::factory()
        ->hasAttached($this->benefit)
        ->create();
});

it('returns true for a customer with an active subscription that has the benefit', function () {
    Subscription::factory()->active()->create([
        'customer_id' => $this->customer->id,
        'membership_plan_id' => $this->plan->id,
    ]);

    $result = new CustomerHasActiveBenefit()->execute($this->customer, Benefit::CEMA_COURSE_ACCESS);

    expect($result)->toBeTrue();
});

it('returns false for a customer with an expired subscription', function () {
    Subscription::factory()->expired()->create([
        'customer_id' => $this->customer->id,
        'membership_plan_id' => $this->plan->id,
    ]);

    $result = new CustomerHasActiveBenefit()->execute($this->customer, Benefit::CEMA_COURSE_ACCESS);

    expect($result)->toBeFalse();
});

it('returns false for a customer with no subscription', function () {
    $result = new CustomerHasActiveBenefit()->execute($this->customer, Benefit::CEMA_COURSE_ACCESS);

    expect($result)->toBeFalse();
});

it('returns false when the active subscription does not have the requested benefit', function () {
    $otherBenefit = Benefit::factory()->create(['code' => 'some_other_benefit']);
    $planWithoutCema = MembershipPlan::factory()
        ->hasAttached($otherBenefit)
        ->create();

    Subscription::factory()->active()->create([
        'customer_id' => $this->customer->id,
        'membership_plan_id' => $planWithoutCema->id,
    ]);

    $result = new CustomerHasActiveBenefit()->execute($this->customer, Benefit::CEMA_COURSE_ACCESS);

    expect($result)->toBeFalse();
});

it('returns true for private_media_access benefit when subscription is active', function () {
    $mediaBenefit = Benefit::factory()->create(['code' => Benefit::PRIVATE_MEDIA_ACCESS]);
    $mediaPlan = MembershipPlan::factory()
        ->hasAttached($mediaBenefit)
        ->create();

    Subscription::factory()->active()->create([
        'customer_id' => $this->customer->id,
        'membership_plan_id' => $mediaPlan->id,
    ]);

    $result = new CustomerHasActiveBenefit()->execute($this->customer, Benefit::PRIVATE_MEDIA_ACCESS);

    expect($result)->toBeTrue();
});
