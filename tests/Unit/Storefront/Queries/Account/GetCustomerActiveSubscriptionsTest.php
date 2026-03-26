<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Customer as LunarCustomer;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Models\Customer;
use Testa\Models\Membership\MembershipPlan;
use Testa\Models\Membership\Subscription;
use Testa\Storefront\Queries\Account\GetCustomerActiveSubscriptions;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);

    $this->customer = Customer::find(LunarCustomer::factory()->create()->id);
    $this->plan = MembershipPlan::factory()->create();
});

it('returns a collection', function () {
    $result = new GetCustomerActiveSubscriptions()->execute($this->customer);

    expect($result)->toBeInstanceOf(Collection::class);
});

it('returns active subscriptions for the customer', function () {
    Subscription::factory()->active()->create([
        'customer_id' => $this->customer->id,
        'membership_plan_id' => $this->plan->id,
    ]);

    $result = new GetCustomerActiveSubscriptions()->execute($this->customer);

    expect($result)->toHaveCount(1);
});

it('excludes cancelled subscriptions', function () {
    Subscription::factory()->cancelled()->create([
        'customer_id' => $this->customer->id,
        'membership_plan_id' => $this->plan->id,
    ]);

    $result = new GetCustomerActiveSubscriptions()->execute($this->customer);

    expect($result)->toBeEmpty();
});

it('excludes expired subscriptions', function () {
    Subscription::factory()->expired()->create([
        'customer_id' => $this->customer->id,
        'membership_plan_id' => $this->plan->id,
    ]);

    $result = new GetCustomerActiveSubscriptions()->execute($this->customer);

    expect($result)->toBeEmpty();
});

it('does not return subscriptions from other customers', function () {
    $otherCustomer = Customer::find(LunarCustomer::factory()->create()->id);
    Subscription::factory()->active()->create([
        'customer_id' => $otherCustomer->id,
        'membership_plan_id' => $this->plan->id,
    ]);

    $result = new GetCustomerActiveSubscriptions()->execute($this->customer);

    expect($result)->toBeEmpty();
});
