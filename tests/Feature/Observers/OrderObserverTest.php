<?php

use Illuminate\Support\Facades\Schema;
use Lunar\Base\ValueObjects\Cart\TaxBreakdown;
use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\Customer;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Order;
use Lunar\Models\Price;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\ProductType;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Lunar\Models\TaxRate;
use Lunar\Models\TaxRateAmount;
use Lunar\Models\TaxZone;
use Lunar\Models\TaxZoneCountry;
use Testa\Models\Education\Course;
use Testa\Models\Membership\Benefit;
use Testa\Models\Membership\MembershipPlan;
use Testa\Models\Membership\MembershipTier;
use Testa\Models\Membership\Subscription;
use Testa\Observers\CourseObserver;
use Testa\Observers\MembershipTierObserver;

beforeEach(function () {
    Schema::table('users', function ($table) {
        $table->dropColumn('name');
        $table->string('first_name')->after('id');
        $table->string('last_name')->after('first_name');
    });

    config(['auth.providers.users.model' => \Testa\Tests\Stubs\User::class]);

    $this->language = Language::factory()->create(['default' => true]);
    $this->currency = Currency::factory()->create([
        'default' => true,
        'decimal_places' => 2,
        'exchange_rate' => 1,
    ]);
    $this->channel = Channel::factory()->create(['default' => true]);
    $this->taxClass = TaxClass::factory()->create(['default' => true]);
    $this->customerGroup = CustomerGroup::factory()->create(['default' => true]);

    $this->country = Country::factory()->create();
    $this->taxZone = TaxZone::factory()->create(['default' => true, 'zone_type' => 'country']);
    TaxZoneCountry::factory()->create([
        'tax_zone_id' => $this->taxZone->id,
        'country_id' => $this->country->id,
    ]);
    $this->taxRate = TaxRate::factory()->create(['tax_zone_id' => $this->taxZone->id]);
    TaxRateAmount::factory()->create([
        'tax_rate_id' => $this->taxRate->id,
        'tax_class_id' => $this->taxClass->id,
        'percentage' => 21,
    ]);

    $userModel = config('auth.providers.users.model');
    $userModel::unguard();
    $this->user = $userModel::create([
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
    $userModel::reguard();

    $this->customer = Customer::factory()->create();
    $this->customer->users()->attach($this->user);

    // Required by CourseObserver when Course::factory() fires
    $productOption = ProductOption::factory()->create([
        'handle' => CourseObserver::RATE_PRODUCT_OPTION_HANDLE,
    ]);
    ProductOptionValue::factory()->create(['product_option_id' => $productOption->id]);
});

// Helper: create tier+plan via observers, return [plan, variant]
function createMembershipPlanWithVariant(): array
{
    // MembershipTierObserver creates product, MembershipPlanObserver creates variant
    $tier = MembershipTier::create([
        'name' => 'Test Tier',
        'description' => 'Test tier',
    ]);
    $tier->refresh();

    $plan = MembershipPlan::create([
        'membership_tier_id' => $tier->id,
        'name' => 'Test Plan',
        'description' => 'Test plan',
        'billing_interval' => MembershipPlan::BILLING_INTERVAL_YEARLY,
    ]);
    $plan->refresh();

    $variant = ProductVariant::find($plan->variant_id);

    return [$plan, $variant];
}

// Helper: create order line for a membership variant
function createMembershipOrderLine(Order $order, ProductVariant $variant): void
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
}

describe('OrderObserver subscription activation', function () {
    it('creates a subscription when order status changes to payment-received', function () {
        [$plan, $variant] = createMembershipPlanWithVariant();

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'awaiting-payment',
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);
        createMembershipOrderLine($order, $variant);

        $order->update(['status' => 'payment-received']);

        expect(Subscription::where('customer_id', $this->customer->id)
            ->where('membership_plan_id', $plan->id)
            ->where('status', Subscription::STATUS_ACTIVE)
            ->exists())->toBeTrue();

        $order->refresh();
        expect($order->was_redeemed)->toBeTruthy();
    });

    it('creates a subscription when order status changes to dispatched', function () {
        [$plan, $variant] = createMembershipPlanWithVariant();

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'awaiting-payment',
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);
        createMembershipOrderLine($order, $variant);

        $order->update(['status' => 'dispatched']);

        expect(Subscription::where('customer_id', $this->customer->id)
            ->where('membership_plan_id', $plan->id)
            ->exists())->toBeTrue();
    });

    it('does not activate subscription for non-valid status', function () {
        [$plan, $variant] = createMembershipPlanWithVariant();

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'awaiting-payment',
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);
        createMembershipOrderLine($order, $variant);

        $order->update(['status' => 'cancelled']);

        expect(Subscription::where('customer_id', $this->customer->id)->exists())->toBeFalse();
    });

    it('does not activate subscription when order was already redeemed', function () {
        [$plan, $variant] = createMembershipPlanWithVariant();

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'awaiting-payment',
            'was_redeemed' => true,
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);
        createMembershipOrderLine($order, $variant);

        $order->update(['status' => 'payment-received']);

        expect(Subscription::where('customer_id', $this->customer->id)->exists())->toBeFalse();
    });

    it('extends existing subscription when one is already active', function () {
        [$plan, $variant] = createMembershipPlanWithVariant();

        $existingExpiry = now()->addMonths(6);
        $existingOrder = Order::factory()->create([
            'user_id' => $this->user->id,
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);
        Subscription::factory()->create([
            'customer_id' => $this->customer->id,
            'membership_plan_id' => $plan->id,
            'order_id' => $existingOrder->id,
            'status' => Subscription::STATUS_ACTIVE,
            'started_at' => now(),
            'expires_at' => $existingExpiry,
        ]);

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'awaiting-payment',
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);
        createMembershipOrderLine($order, $variant);

        $order->update(['status' => 'payment-received']);

        $newSubscription = Subscription::where('customer_id', $this->customer->id)
            ->where('order_id', $order->id)
            ->first();

        expect($newSubscription)->not->toBeNull();
        $expectedStart = $existingExpiry->copy()->addDay();
        $expectedExpiry = $existingExpiry->copy()->addYear();
        expect($newSubscription->started_at->format('Y-m-d'))
            ->toBe($expectedStart->format('Y-m-d'));
        expect($newSubscription->expires_at->format('Y-m-d'))
            ->toBe($expectedExpiry->format('Y-m-d'));
    });

    it('applies customer group benefit on subscription activation', function () {
        [$plan, $variant] = createMembershipPlanWithVariant();

        $customerGroup = CustomerGroup::factory()->create(['name' => 'Members']);
        $benefit = Benefit::factory()->customerGroup()->create([
            'customer_group_id' => $customerGroup->id,
        ]);
        $plan->benefits()->attach($benefit);

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'awaiting-payment',
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);
        createMembershipOrderLine($order, $variant);

        $order->update(['status' => 'payment-received']);

        expect($this->customer->fresh()->customerGroups->pluck('id'))
            ->toContain($customerGroup->id);
    });

    it('skips non-membership order lines when activating subscriptions', function () {
        $regularProductType = ProductType::factory()->create();
        $product = Product::factory()->create([
            'product_type_id' => $regularProductType->id,
        ]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'tax_class_id' => $this->taxClass->id,
        ]);

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'awaiting-payment',
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);
        $order->lines()->create([
            'purchasable_type' => 'product_variant',
            'purchasable_id' => $variant->id,
            'type' => 'physical',
            'description' => 'Book',
            'identifier' => $variant->sku,
            'unit_price' => 1000,
            'unit_quantity' => 1,
            'quantity' => 1,
            'sub_total' => 1000,
            'discount_total' => 0,
            'tax_breakdown' => new TaxBreakdown,
            'tax_total' => 0,
            'total' => 1000,
        ]);

        $order->update(['status' => 'payment-received']);

        expect(Subscription::count())->toBe(0);
        $order->refresh();
        expect($order->was_redeemed)->toBeFalsy();
    });
});

describe('OrderObserver course activation', function () {
    it('enrolls customer in course when order is paid', function () {
        // Create course - CourseObserver will auto-create a product with PRODUCT_TYPE_ID = 3
        $course = Course::factory()->create();
        $course->refresh();

        // Use the product that the observer created
        $product = Product::find($course->purchasable_id);
        $variant = $product->variants->first();

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'awaiting-payment',
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);
        $order->lines()->create([
            'purchasable_type' => 'product_variant',
            'purchasable_id' => $variant->id,
            'type' => 'physical',
            'description' => 'Course Enrollment',
            'identifier' => $variant->sku,
            'unit_price' => 3000,
            'unit_quantity' => 1,
            'quantity' => 1,
            'sub_total' => 3000,
            'discount_total' => 0,
            'tax_breakdown' => new TaxBreakdown,
            'tax_total' => 0,
            'total' => 3000,
        ]);

        $order->update(['status' => 'payment-received']);

        $testaCustomer = \Testa\Models\Customer::find($this->customer->id);
        expect($testaCustomer->courses->pluck('id'))->toContain($course->id);

        $order->refresh();
        expect($order->was_redeemed)->toBeTruthy();
    });

    it('does not enroll customer in same course twice', function () {
        $course = Course::factory()->create();
        $course->refresh();

        $product = Product::find($course->purchasable_id);
        $variant = $product->variants->first();

        // Enroll customer first
        $testaCustomer = \Testa\Models\Customer::find($this->customer->id);
        $testaCustomer->courses()->attach($course);

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'awaiting-payment',
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);
        $order->lines()->create([
            'purchasable_type' => 'product_variant',
            'purchasable_id' => $variant->id,
            'type' => 'physical',
            'description' => 'Course Enrollment',
            'identifier' => $variant->sku,
            'unit_price' => 3000,
            'unit_quantity' => 1,
            'quantity' => 1,
            'sub_total' => 3000,
            'discount_total' => 0,
            'tax_breakdown' => new TaxBreakdown,
            'tax_total' => 0,
            'total' => 3000,
        ]);

        $order->update(['status' => 'payment-received']);

        $testaCustomer->refresh();
        expect($testaCustomer->courses)->toHaveCount(1);

        $order->refresh();
        expect($order->was_redeemed)->toBeFalsy();
    });

    it('does not activate when status is not dirty', function () {
        $course = Course::factory()->create();
        $course->refresh();

        $product = Product::find($course->purchasable_id);
        $variant = $product->variants->first();

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'payment-received',
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);
        $order->lines()->create([
            'purchasable_type' => 'product_variant',
            'purchasable_id' => $variant->id,
            'type' => 'physical',
            'description' => 'Course Enrollment',
            'identifier' => $variant->sku,
            'unit_price' => 3000,
            'unit_quantity' => 1,
            'quantity' => 1,
            'sub_total' => 3000,
            'discount_total' => 0,
            'tax_breakdown' => new TaxBreakdown,
            'tax_total' => 0,
            'total' => 3000,
        ]);

        // Update something other than status
        $order->update(['notes' => 'Test note']);

        $testaCustomer = \Testa\Models\Customer::find($this->customer->id);
        expect($testaCustomer->courses)->toHaveCount(0);
    });
});
