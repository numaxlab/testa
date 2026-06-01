<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Lunar\Models\TaxRate;
use Lunar\Models\TaxRateAmount;
use Lunar\Models\TaxZone;
use Lunar\Models\TaxZoneCountry;
use Testa\Models\Customer;
use Testa\Models\Media\Document;
use Testa\Models\Media\Visibility;
use Testa\Models\Membership\Benefit;
use Testa\Models\Membership\MembershipPlan;
use Testa\Models\Membership\MembershipTier;
use Testa\Models\Membership\Subscription;
use Testa\Tests\Stubs\User;

beforeEach(function () {
    Schema::table('users', function ($table) {
        $table->dropColumn('name');
        $table->string('first_name')->after('id');
        $table->string('last_name')->after('first_name');
    });

    config(['auth.providers.users.model' => User::class]);

    Language::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true, 'decimal_places' => 2]);
    Channel::factory()->create(['default' => true]);
    TaxClass::factory()->create(['default' => true]);
    CustomerGroup::factory()->create(['default' => true]);

    $country = Country::factory()->create();
    $taxZone = TaxZone::factory()->create(['default' => true, 'zone_type' => 'country']);
    TaxZoneCountry::factory()->create(['tax_zone_id' => $taxZone->id, 'country_id' => $country->id]);
    $taxRate = TaxRate::factory()->create(['tax_zone_id' => $taxZone->id]);
    TaxRateAmount::factory()->create([
        'tax_rate_id' => $taxRate->id,
        'tax_class_id' => TaxClass::getDefault()->id,
        'percentage' => 21,
    ]);

    Storage::fake();

    $this->document = Document::factory()->create([
        'visibility' => Visibility::MEMBERS_ONLY->value,
        'is_published' => true,
        'path' => 'documents/test-file.pdf',
    ]);

    Storage::put('documents/test-file.pdf', 'PDF content for testing');
});

// ─── Helpers ─────────────────────────────────────────────────────────────────

function downloadCreateUser(): User
{
    $userModel = config('auth.providers.users.model');
    $userModel::unguard();
    $user = $userModel::create([
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'download-test-'.uniqid().'@example.com',
        'password' => bcrypt('password'),
    ]);
    $userModel::reguard();

    $customer = Customer::create(['first_name' => 'Test', 'last_name' => 'User']);
    $customer->users()->attach($user);

    return $user;
}

function downloadGiveActiveMediaMembership(User $user): void
{
    $customer = Customer::find($user->latestCustomer()->id);
    $benefit = Benefit::factory()->create(['code' => Benefit::PRIVATE_MEDIA_ACCESS]);
    $plan = MembershipPlan::factory()->create([
        'membership_tier_id' => MembershipTier::factory()->create()->id,
    ]);
    $plan->benefits()->attach($benefit);
    Subscription::factory()->active()->create([
        'customer_id' => $customer->id,
        'membership_plan_id' => $plan->id,
    ]);
}

function downloadGiveExpiredMediaMembership(User $user): void
{
    $customer = Customer::find($user->latestCustomer()->id);
    $benefit = Benefit::factory()->create(['code' => Benefit::PRIVATE_MEDIA_ACCESS]);
    $plan = MembershipPlan::factory()->create([
        'membership_tier_id' => MembershipTier::factory()->create()->id,
    ]);
    $plan->benefits()->attach($benefit);
    Subscription::factory()->expired()->create([
        'customer_id' => $customer->id,
        'membership_plan_id' => $plan->id,
    ]);
}

// ─── Tests ───────────────────────────────────────────────────────────────────

describe('DocumentDownloadController', function () {
    it('returns 403 for guest trying to download a members_only document', function () {
        $this->get(route('testa.storefront.media.documents.download', $this->document))
            ->assertForbidden();
    });

    it('returns 403 for authenticated user with no active membership', function () {
        $user = downloadCreateUser();

        $this->actingAs($user)
            ->get(route('testa.storefront.media.documents.download', $this->document))
            ->assertForbidden();
    });

    it('returns 403 for authenticated user with expired membership', function () {
        $user = downloadCreateUser();
        downloadGiveExpiredMediaMembership($user);

        $this->actingAs($user)
            ->get(route('testa.storefront.media.documents.download', $this->document))
            ->assertForbidden();
    });

    it('serves the file for authenticated user with active membership', function () {
        $user = downloadCreateUser();
        downloadGiveActiveMediaMembership($user);

        $this->actingAs($user)
            ->get(route('testa.storefront.media.documents.download', $this->document))
            ->assertOk();
    });

    it('allows guests to download public documents without membership', function () {
        $publicDocument = Document::factory()->create([
            'visibility' => Visibility::PUBLIC->value,
            'is_published' => true,
            'path' => 'documents/public-file.pdf',
        ]);
        Storage::put('documents/public-file.pdf', 'Public PDF content');

        $this->get(route('testa.storefront.media.documents.download', $publicDocument))
            ->assertOk();
    });
});
