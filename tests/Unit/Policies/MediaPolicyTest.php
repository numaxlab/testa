<?php

use Illuminate\Foundation\Auth\User as AuthUser;
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
use Testa\Policies\MediaPolicy;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);
});

it('allows public documents for unauthenticated users', function () {
    $document = Document::factory()->public()->create();

    $allowed = (new MediaPolicy)->view(null, $document);

    expect($allowed)->toBeTrue();
});

it('denies private documents for unauthenticated users', function () {
    $document = Document::factory()->private()->create();

    $allowed = (new MediaPolicy)->view(null, $document);

    expect($allowed)->toBeFalse();
});

it('denies members_only documents for unauthenticated users', function () {
    $document = Document::factory()->create(['visibility' => Visibility::MEMBERS_ONLY->value]);

    $allowed = (new MediaPolicy)->view(null, $document);

    expect($allowed)->toBeFalse();
});

it('allows members_only documents for customers with active membership benefit', function () {
    $lunarCustomer = LunarCustomer::factory()->create();
    $customer = Customer::find($lunarCustomer->id);

    $benefit = Benefit::factory()->create(['code' => Benefit::PRIVATE_MEDIA_ACCESS]);
    $plan = MembershipPlan::factory()->hasAttached($benefit)->create();
    Subscription::factory()->active()->create([
        'customer_id' => $customer->id,
        'membership_plan_id' => $plan->id,
    ]);

    $document = Document::factory()->create(['visibility' => Visibility::MEMBERS_ONLY->value]);

    // Create a mock user that returns our customer
    $user = Mockery::mock(AuthUser::class);
    $user->shouldReceive('latestCustomer')->andReturn($customer);

    $allowed = (new MediaPolicy)->view($user, $document);

    expect($allowed)->toBeTrue();
});

it('denies members_only documents for customers with expired membership', function () {
    $lunarCustomer = LunarCustomer::factory()->create();
    $customer = Customer::find($lunarCustomer->id);

    $benefit = Benefit::factory()->create(['code' => Benefit::PRIVATE_MEDIA_ACCESS]);
    $plan = MembershipPlan::factory()->hasAttached($benefit)->create();
    Subscription::factory()->expired()->create([
        'customer_id' => $customer->id,
        'membership_plan_id' => $plan->id,
    ]);

    $document = Document::factory()->create(['visibility' => Visibility::MEMBERS_ONLY->value]);

    $user = Mockery::mock(AuthUser::class);
    $user->shouldReceive('latestCustomer')->andReturn($customer);

    $allowed = (new MediaPolicy)->view($user, $document);

    expect($allowed)->toBeFalse();
});
