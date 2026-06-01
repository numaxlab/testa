<?php

use Illuminate\Support\Facades\Schema;
use Lunar\Base\ShippingManifestInterface;
use Lunar\Facades\CartSession;
use Lunar\Models\Cart;
use Lunar\Models\CartLine;
use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Price;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
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
use Testa\Settings\PaymentSettings;
use Testa\Storefront\Livewire\Checkout\ShippingAndPaymentPage;
use Testa\Tests\Stubs\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    Schema::table('users', function ($table) {
        $table->dropColumn('name');
        $table->string('first_name')->after('id');
        $table->string('last_name')->after('first_name');
    });

    Schema::create('lunar_shipping_methods', function ($table) {
        $table->bigIncrements('id');
        $table->string('name');
        $table->text('description')->nullable();
        $table->string('code')->index()->nullable();
        $table->boolean('enabled')->default(true);
        $table->boolean('stock_available')->default(false);
        $table->time('cutoff')->nullable();
        $table->json('data')->nullable();
        $table->string('driver');
        $table->timestamps();
    });

    config(['auth.providers.users.model' => User::class]);

    Language::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true, 'decimal_places' => 2, 'exchange_rate' => 1]);
    $this->channel = Channel::factory()->create(['default' => true]);
    $this->taxClass = TaxClass::factory()->create(['default' => true]);
    CustomerGroup::factory()->create(['default' => true]);

    ProductType::factory()->create(['id' => 1]);

    $this->country = Country::factory()->create();
    $taxZone = TaxZone::factory()->create(['default' => true, 'zone_type' => 'country']);
    TaxZoneCountry::factory()->create(['tax_zone_id' => $taxZone->id, 'country_id' => $this->country->id]);
    $taxRate = TaxRate::factory()->create(['tax_zone_id' => $taxZone->id]);
    TaxRateAmount::factory()->create([
        'tax_rate_id' => $taxRate->id,
        'tax_class_id' => $this->taxClass->id,
        'percentage' => 21,
    ]);

    $mockSettings = Mockery::mock(PaymentSettings::class);
    $mockSettings->store = ['card', 'transfer', 'credit'];
    app()->instance(PaymentSettings::class, $mockSettings);

    $manifestMock = Mockery::mock(ShippingManifestInterface::class)->shouldIgnoreMissing();
    $manifestMock->allows('getOptions')->andReturn(collect([]));
    $manifestMock->allows('getShippingOption')->andReturn(null);
    app()->instance(ShippingManifestInterface::class, $manifestMock);
});

// ─── Helpers ──────────────────────────────────────────────────────────────────

function creditCreateUser(): mixed
{
    $userModel = config('auth.providers.users.model');
    $userModel::unguard();
    $user = $userModel::create([
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'credit-test@example.com',
        'password' => bcrypt('password'),
    ]);
    $userModel::reguard();

    $customer = Lunar\Models\Customer::create([
        'first_name' => 'Test',
        'last_name' => 'User',
    ]);
    $customer->users()->attach($user);

    return $user;
}

function creditCreateCart(mixed $user): Cart
{
    $cart = Cart::factory()->create([
        'currency_id' => Currency::getDefault()->id,
        'channel_id' => Channel::getDefault()->id,
        'user_id' => $user->id,
    ]);

    $product = Product::factory()->create(['product_type_id' => 1]);
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'tax_class_id' => TaxClass::getDefault()->id,
    ]);
    Price::factory()->create([
        'priceable_type' => ProductVariant::morphName(),
        'priceable_id' => $variant->id,
        'currency_id' => Currency::getDefault()->id,
        'min_quantity' => 1,
        'price' => 1000,
    ]);
    CartLine::factory()->create([
        'cart_id' => $cart->id,
        'purchasable_type' => ProductVariant::morphName(),
        'purchasable_id' => $variant->id,
        'quantity' => 1,
    ]);

    return $cart->fresh(['lines']);
}

function creditGiveActiveMembership(mixed $user): void
{
    $customer = Customer::find($user->latestCustomer()->id);

    $benefit = Benefit::factory()->create(['code' => Benefit::CREDIT_PAYMENT_TYPE]);
    $plan = MembershipPlan::factory()->create([
        'membership_tier_id' => MembershipTier::factory()->create()->id,
    ]);
    $plan->benefits()->attach($benefit);

    Subscription::factory()->active()->create([
        'customer_id' => $customer->id,
        'membership_plan_id' => $plan->id,
    ]);
}

function creditGiveExpiredMembership(mixed $user): void
{
    $customer = Customer::find($user->latestCustomer()->id);

    $benefit = Benefit::factory()->create(['code' => Benefit::CREDIT_PAYMENT_TYPE]);
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

describe('CreditMembershipBenefit', function () {
    it('excludes credit payment type when customer has no active membership', function () {
        $user = creditCreateUser();
        $this->actingAs($user);
        $cart = creditCreateCart($user);
        CartSession::use($cart);

        $component = livewire(ShippingAndPaymentPage::class);

        expect($component->get('paymentTypes'))->not->toContain('credit');
    });

    it('includes credit payment type when customer has active membership with credit benefit', function () {
        $user = creditCreateUser();
        creditGiveActiveMembership($user);
        $this->actingAs($user);
        $cart = creditCreateCart($user);
        CartSession::use($cart);

        $component = livewire(ShippingAndPaymentPage::class);

        expect($component->get('paymentTypes'))->toContain('credit');
    });

    it('excludes credit payment type when membership has expired', function () {
        $user = creditCreateUser();
        creditGiveExpiredMembership($user);
        $this->actingAs($user);
        $cart = creditCreateCart($user);
        CartSession::use($cart);

        $component = livewire(ShippingAndPaymentPage::class);

        expect($component->get('paymentTypes'))->not->toContain('credit');
    });

    it('excludes credit for unauthenticated users', function () {
        $user = creditCreateUser();
        $cart = creditCreateCart($user);
        CartSession::use($cart);

        // Not acting as any user
        $component = livewire(ShippingAndPaymentPage::class);

        expect($component->get('paymentTypes'))->not->toContain('credit');
    });
});
