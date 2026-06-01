<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Customer as LunarCustomer;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Console\Commands\SyncMembershipBenefits;
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

    $this->customer = Customer::find(LunarCustomer::factory()->create()->id);
    $this->customerGroup = CustomerGroup::factory()->create();

    $this->benefit = Benefit::factory()->create([
        'code' => Benefit::CUSTOMER_GROUP,
        'customer_group_id' => $this->customerGroup->id,
    ]);

    $this->plan = MembershipPlan::factory()
        ->hasAttached($this->benefit)
        ->create();
});

it('attaches customer group when subscription is active', function () {
    $subscription = Subscription::factory()->active()->create([
        'customer_id' => $this->customer->id,
        'membership_plan_id' => $this->plan->id,
    ]);

    new SyncMembershipBenefits()->sync($this->customer);

    expect(
        $this->customer->customerGroups()->where('lunar_customer_groups.id', $this->customerGroup->id)->exists()
    )->toBeTrue();

    expect(
        DB::table('membership_customer_group_assignments')
            ->where('customer_id', $this->customer->id)
            ->where('subscription_id', $subscription->id)
            ->where('benefit_id', $this->benefit->id)
            ->where('customer_group_id', $this->customerGroup->id)
            ->whereNull('revoked_at')
            ->exists()
    )->toBeTrue();
});

it('detaches customer group when subscription is expired', function () {
    $subscription = Subscription::factory()->expired()->create([
        'customer_id' => $this->customer->id,
        'membership_plan_id' => $this->plan->id,
    ]);

    // Pre-attach the group (simulating previously synced state)
    $this->customer->customerGroups()->syncWithoutDetaching([$this->customerGroup->id]);
    DB::table('membership_customer_group_assignments')->insert([
        'customer_id' => $this->customer->id,
        'subscription_id' => $subscription->id,
        'benefit_id' => $this->benefit->id,
        'customer_group_id' => $this->customerGroup->id,
        'expires_at' => now()->subDay(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    new SyncMembershipBenefits()->sync($this->customer);

    expect(
        $this->customer->customerGroups()->where('lunar_customer_groups.id', $this->customerGroup->id)->exists()
    )->toBeFalse();

    expect(
        DB::table('membership_customer_group_assignments')
            ->where('customer_id', $this->customer->id)
            ->where('subscription_id', $subscription->id)
            ->whereNotNull('revoked_at')
            ->exists()
    )->toBeTrue();
});

it('is idempotent — running sync twice does not duplicate assignments', function () {
    Subscription::factory()->active()->create([
        'customer_id' => $this->customer->id,
        'membership_plan_id' => $this->plan->id,
    ]);

    new SyncMembershipBenefits()->sync($this->customer);
    new SyncMembershipBenefits()->sync($this->customer);

    $count = DB::table('membership_customer_group_assignments')
        ->where('customer_id', $this->customer->id)
        ->count();

    expect($count)->toBe(1);
});

it('does not touch customer groups not managed by membership sync', function () {
    $manualGroup = CustomerGroup::factory()->create();
    // Manually attach a group (not via membership benefit)
    $this->customer->customerGroups()->syncWithoutDetaching([$manualGroup->id]);

    Subscription::factory()->expired()->create([
        'customer_id' => $this->customer->id,
        'membership_plan_id' => $this->plan->id,
    ]);

    new SyncMembershipBenefits()->sync($this->customer);

    // The manually attached group should still be there
    expect(
        $this->customer->customerGroups()->where('lunar_customer_groups.id', $manualGroup->id)->exists()
    )->toBeTrue();
});
