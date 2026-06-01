<?php

use Illuminate\Support\Facades\Schema;
use Lunar\Base\ValueObjects\Cart\TaxBreakdown;
use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\Customer as LunarCustomer;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Order;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Lunar\Models\TaxRate;
use Lunar\Models\TaxRateAmount;
use Lunar\Models\TaxZone;
use Lunar\Models\TaxZoneCountry;
use Testa\Models\Customer;
use Testa\Models\Membership\Benefit;
use Testa\Models\Membership\MembershipPlan;
use Testa\Models\Membership\MembershipTier;
use Testa\Models\Membership\Subscription;
use Testa\Observers\CourseObserver;
use Testa\Tests\Stubs\User;

beforeEach(function () {
    Schema::table('users', function ($table) {
        $table->dropColumn('name');
        $table->string('first_name')->after('id');
        $table->string('last_name')->after('first_name');
    });

    config(['auth.providers.users.model' => User::class]);

    Language::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true, 'decimal_places' => 2, 'exchange_rate' => 1]);
    $this->channel = Channel::factory()->create(['default' => true]);
    $this->taxClass = TaxClass::factory()->create(['default' => true]);
    CustomerGroup::factory()->create(['default' => true]);

    $this->country = Country::factory()->create();
    $taxZone = TaxZone::factory()->create(['default' => true, 'zone_type' => 'country']);
    TaxZoneCountry::factory()->create(['tax_zone_id' => $taxZone->id, 'country_id' => $this->country->id]);
    $taxRate = TaxRate::factory()->create(['tax_zone_id' => $taxZone->id]);
    TaxRateAmount::factory()->create([
        'tax_rate_id' => $taxRate->id,
        'tax_class_id' => $this->taxClass->id,
        'percentage' => 21,
    ]);

    // CourseObserver requires this product option on Course::factory()
    $productOption = ProductOption::factory()->create([
        'handle' => CourseObserver::RATE_PRODUCT_OPTION_HANDLE,
    ]);
    ProductOptionValue::factory()->create(['product_option_id' => $productOption->id]);

    $userModel = config('auth.providers.users.model');
    $userModel::unguard();
    $this->user = $userModel::create([
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
    $userModel::reguard();

    $this->lunarCustomer = LunarCustomer::factory()->create();
    $this->lunarCustomer->users()->attach($this->user);
    $this->customer = Customer::find($this->lunarCustomer->id);

    $this->membershipGroup = CustomerGroup::factory()->create(['name' => 'Members']);
    $this->benefit = Benefit::factory()->create([
        'code' => Benefit::CUSTOMER_GROUP,
        'customer_group_id' => $this->membershipGroup->id,
    ]);
});

// Helper: create tier + plan via observers, return [plan, variant]
function integrationCreatePlan(Benefit $benefit): array
{
    $tier = MembershipTier::create([
        'name' => 'Test Tier',
        'description' => 'Test',
    ]);
    $tier->refresh();

    $plan = MembershipPlan::create([
        'membership_tier_id' => $tier->id,
        'name' => 'Test Plan',
        'description' => 'Test',
        'billing_interval' => MembershipPlan::BILLING_INTERVAL_YEARLY,
    ]);
    $plan->refresh();
    $plan->benefits()->attach($benefit);

    return [$plan, ProductVariant::find($plan->variant_id)];
}

// Helper: place an order line for a membership variant and change status
function integrationPayMembership(Order $order, ProductVariant $variant): void
{
    $order->lines()->create([
        'purchasable_type' => 'product_variant',
        'purchasable_id' => $variant->id,
        'type' => 'physical',
        'description' => 'Membership',
        'identifier' => $variant->sku,
        'unit_price' => 5000,
        'unit_quantity' => 1,
        'quantity' => 1,
        'sub_total' => 5000,
        'discount_total' => 0,
        'tax_breakdown' => new TaxBreakdown,
        'tax_total' => 0,
        'total' => 5000,
    ]);

    $order->update(['status' => 'payment-received']);
}

describe('MembershipBenefitsSync integration', function () {
    it('attaches membership customer group when order is paid', function () {
        [$plan, $variant] = integrationCreatePlan($this->benefit);

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'awaiting-payment',
            'currency_code' => Currency::getDefault()->code,
            'channel_id' => $this->channel->id,
        ]);

        integrationPayMembership($order, $variant);

        expect(
            $this->customer->fresh()->customerGroups->pluck('id')
        )->toContain($this->membershipGroup->id);

        expect(
            DB::table('membership_customer_group_assignments')
                ->where('customer_id', $this->customer->id)
                ->where('customer_group_id', $this->membershipGroup->id)
                ->whereNull('revoked_at')
                ->exists()
        )->toBeTrue();
    });

    it('revokes customer group when subscription expires and scheduler runs', function () {
        // Pre-attach: simulate previously synced active subscription now expired
        $this->customer->customerGroups()->syncWithoutDetaching([$this->membershipGroup->id]);

        $plan = MembershipPlan::factory()->create([
            'membership_tier_id' => MembershipTier::factory()->create()->id,
        ]);
        $plan->benefits()->attach($this->benefit);

        $subscription = Subscription::factory()->expired()->create([
            'customer_id' => $this->customer->id,
            'membership_plan_id' => $plan->id,
        ]);

        DB::table('membership_customer_group_assignments')->insert([
            'customer_id' => $this->customer->id,
            'subscription_id' => $subscription->id,
            'benefit_id' => $this->benefit->id,
            'customer_group_id' => $this->membershipGroup->id,
            'expires_at' => now()->subDay(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Run sync command (simulates hourly scheduler)
        $this->artisan('testa:sync-membership-benefits')
            ->assertSuccessful();

        expect(
            $this->customer->fresh()->customerGroups->pluck('id')
        )->not->toContain($this->membershipGroup->id);

        expect(
            DB::table('membership_customer_group_assignments')
                ->where('customer_id', $this->customer->id)
                ->whereNotNull('revoked_at')
                ->exists()
        )->toBeTrue();
    });

    it('does not revoke manually assigned groups not managed by membership sync', function () {
        $manualGroup = CustomerGroup::factory()->create(['name' => 'Manual Group']);
        $this->customer->customerGroups()->syncWithoutDetaching([$manualGroup->id]);

        // Run sync with no membership assignments
        $this->artisan('testa:sync-membership-benefits')
            ->assertSuccessful();

        expect(
            $this->customer->fresh()->customerGroups->pluck('id')
        )->toContain($manualGroup->id);
    });
});
