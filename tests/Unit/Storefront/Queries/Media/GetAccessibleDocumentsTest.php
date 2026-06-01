<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Customer as LunarCustomer;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Models\Customer;
use Testa\Models\Media\Document;
use Testa\Models\Media\Visibility;
use Testa\Models\Membership\Benefit;
use Testa\Models\Membership\MembershipPlan;
use Testa\Models\Membership\Subscription;
use Testa\Storefront\Queries\Media\GetAccessibleDocuments;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);
});

it('returns published public documents for unauthenticated users', function () {
    Document::factory()->public()->create(['is_published' => true]);
    Document::factory()->create(['visibility' => Visibility::MEMBERS_ONLY->value, 'is_published' => true]);

    $result = new GetAccessibleDocuments()->execute(customer: null);

    expect($result)->toHaveCount(1);
    expect($result->first()->visibility)->toBe(Visibility::PUBLIC);
});

it('returns public and members_only documents for active members', function () {
    $customer = Customer::find(LunarCustomer::factory()->create()->id);

    $benefit = Benefit::factory()->create(['code' => Benefit::PRIVATE_MEDIA_ACCESS]);
    $plan = MembershipPlan::factory()->hasAttached($benefit)->create();
    Subscription::factory()->active()->create([
        'customer_id' => $customer->id,
        'membership_plan_id' => $plan->id,
    ]);

    Document::factory()->public()->create(['is_published' => true]);
    Document::factory()->create(['visibility' => Visibility::MEMBERS_ONLY->value, 'is_published' => true]);
    Document::factory()->private()->create(['is_published' => true]);

    $result = new GetAccessibleDocuments()->execute(customer: $customer);

    expect($result)->toHaveCount(2);
});

it('excludes unpublished documents', function () {
    Document::factory()->public()->create(['is_published' => false]);

    $result = new GetAccessibleDocuments()->execute(customer: null);

    expect($result)->toHaveCount(0);
});
