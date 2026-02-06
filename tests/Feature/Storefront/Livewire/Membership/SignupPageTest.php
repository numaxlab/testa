<?php

use Illuminate\Support\Facades\Schema;
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
use Testa\Models\Membership\MembershipPlan;
use Testa\Models\Membership\MembershipTier;
use Testa\Settings\PaymentSettings;
use Testa\Storefront\Livewire\Membership\SignupPage;

use function Pest\Livewire\livewire;

beforeEach(function () {
    // Modify users table to match expected schema (first_name, last_name instead of name)
    Schema::table('users', function ($table) {
        $table->dropColumn('name');
        $table->string('first_name')->after('id');
        $table->string('last_name')->after('first_name');
    });

    // Configure auth to use our test User model with LunarUser trait
    config(['auth.providers.users.model' => \Testa\Tests\Stubs\User::class]);

    $this->language = Language::factory()->create(['default' => true]);
    $this->currency = Currency::factory()->create(['default' => true, 'decimal_places' => 2]);
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

    // Create membership tier and plan with variant
    $this->productType = ProductType::factory()->create();
    $this->product = Product::factory()->create([
        'product_type_id' => $this->productType->id,
    ]);
    $this->variant = ProductVariant::factory()->create([
        'product_id' => $this->product->id,
        'tax_class_id' => $this->taxClass->id,
    ]);
    Price::factory()->create([
        'priceable_type' => ProductVariant::morphName(),
        'priceable_id' => $this->variant->id,
        'currency_id' => $this->currency->id,
        'min_quantity' => 1,
        'price' => 5000, // 50.00
    ]);

    $this->tier = MembershipTier::factory()->create(['is_published' => true]);
    $this->plan = MembershipPlan::factory()->create([
        'membership_tier_id' => $this->tier->id,
        'is_published' => true,
        'variant_id' => $this->variant->id,
    ]);

    // Mock PaymentSettings
    $mockPaymentSettings = Mockery::mock(PaymentSettings::class);
    $mockPaymentSettings->membership = ['direct-debit'];
    app()->instance(PaymentSettings::class, $mockPaymentSettings);
});

describe('SignupPage for guests', function () {
    it('has registration properties defined', function () {
        $component = new SignupPage();

        expect(property_exists($component, 'first_name'))->toBeTrue();
        expect(property_exists($component, 'last_name'))->toBeTrue();
        expect(property_exists($component, 'email'))->toBeTrue();
        expect(property_exists($component, 'password'))->toBeTrue();
        expect(property_exists($component, 'password_confirmation'))->toBeTrue();
    });

    it('validates registration fields for guests', function () {
        livewire(SignupPage::class)
            ->set('selectedTier', $this->tier->id)
            ->set('selectedPlan', $this->plan->id)
            ->set('paymentType', 'direct-debit')
            ->set('directDebitOwnerName', 'Test Owner')
            ->set('directDebitBankName', 'Test Bank')
            ->set('directDebitIban', 'ES9121000418450200051332')
            ->set('privacy_policy', true)
            // billing.first_name and billing.last_name not needed - copied from registration
            ->set('billing.country_id', $this->country->id)
            ->set('billing.state', 'Madrid')
            ->set('billing.postcode', '28001')
            ->set('billing.city', 'Madrid')
            ->set('billing.line_one', 'Calle Test 123')
            ->call('signup')
            ->assertHasErrors(['first_name', 'last_name', 'email', 'password']);
    });

    it('validates email must be unique', function () {
        $userModel = config('auth.providers.users.model');
        $userModel::unguard();
        $userModel::create([
            'first_name' => 'Existing',
            'last_name' => 'User',
            'email' => 'existing@example.com',
            'password' => bcrypt('password'),
        ]);
        $userModel::reguard();

        livewire(SignupPage::class)
            ->set('selectedTier', $this->tier->id)
            ->set('selectedPlan', $this->plan->id)
            ->set('paymentType', 'direct-debit')
            ->set('directDebitOwnerName', 'Test Owner')
            ->set('directDebitBankName', 'Test Bank')
            ->set('directDebitIban', 'ES9121000418450200051332')
            ->set('privacy_policy', true)
            ->set('first_name', 'Test')
            ->set('last_name', 'User')
            ->set('email', 'existing@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            // billing.first_name and billing.last_name not needed - copied from registration
            ->set('billing.country_id', $this->country->id)
            ->set('billing.state', 'Madrid')
            ->set('billing.postcode', '28001')
            ->set('billing.city', 'Madrid')
            ->set('billing.line_one', 'Calle Test 123')
            ->call('signup')
            ->assertHasErrors(['email']);
    });

    it('validates password confirmation must match', function () {
        livewire(SignupPage::class)
            ->set('selectedTier', $this->tier->id)
            ->set('selectedPlan', $this->plan->id)
            ->set('paymentType', 'direct-debit')
            ->set('directDebitOwnerName', 'Test Owner')
            ->set('directDebitBankName', 'Test Bank')
            ->set('directDebitIban', 'ES9121000418450200051332')
            ->set('privacy_policy', true)
            ->set('first_name', 'Test')
            ->set('last_name', 'User')
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'different_password')
            // billing.first_name and billing.last_name not needed - copied from registration
            ->set('billing.country_id', $this->country->id)
            ->set('billing.state', 'Madrid')
            ->set('billing.postcode', '28001')
            ->set('billing.city', 'Madrid')
            ->set('billing.line_one', 'Calle Test 123')
            ->call('signup')
            ->assertHasErrors(['password']);
    });

    it('creates user and logs in when guest completes signup', function () {
        $userModel = config('auth.providers.users.model');

        livewire(SignupPage::class)
            ->set('selectedTier', $this->tier->id)
            ->set('selectedPlan', $this->plan->id)
            ->set('paymentType', 'direct-debit')
            ->set('directDebitOwnerName', 'Test Owner')
            ->set('directDebitBankName', 'Test Bank')
            ->set('directDebitIban', 'ES9121000418450200051332')
            ->set('privacy_policy', true)
            ->set('first_name', 'New')
            ->set('last_name', 'User')
            ->set('email', 'newuser@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            // billing.first_name, last_name, contact_email are copied from registration
            ->set('billing.country_id', $this->country->id)
            ->set('billing.state', 'Madrid')
            ->set('billing.postcode', '28001')
            ->set('billing.city', 'Madrid')
            ->set('billing.line_one', 'Calle Test 123')
            ->call('signup')
            ->assertHasNoErrors()
            ->assertRedirectContains('checkout/procesar-pago');

        // Verify user was created
        expect($userModel::where('email', 'newuser@example.com')->exists())->toBeTrue();

        // Verify user is logged in
        expect(auth()->check())->toBeTrue();
        expect(auth()->user()->email)->toBe('newuser@example.com');
    });
});

describe('SignupPage for authenticated users', function () {
    it('does not require registration fields for authenticated users', function () {
        $userModel = config('auth.providers.users.model');
        $userModel::unguard();
        $user = $userModel::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'),
        ]);
        $userModel::reguard();

        // Create customer for the user
        $customer = \Lunar\Models\Customer::create([
            'first_name' => 'Test',
            'last_name' => 'User',
        ]);
        $customer->users()->attach($user);

        $this->actingAs($user);

        livewire(SignupPage::class)
            ->set('selectedTier', $this->tier->id)
            ->set('selectedPlan', $this->plan->id)
            ->set('paymentType', 'direct-debit')
            ->set('directDebitOwnerName', 'Test Owner')
            ->set('directDebitBankName', 'Test Bank')
            ->set('directDebitIban', 'ES9121000418450200051332')
            ->set('privacy_policy', true)
            ->set('billing.first_name', 'Test')
            ->set('billing.last_name', 'User')
            ->set('billing.contact_email', 'testuser@example.com')
            ->set('billing.country_id', $this->country->id)
            ->set('billing.state', 'Madrid')
            ->set('billing.postcode', '28001')
            ->set('billing.city', 'Madrid')
            ->set('billing.line_one', 'Calle Test 123')
            ->call('signup')
            ->assertHasNoErrors()
            ->assertRedirectContains('checkout/procesar-pago');
    });

    it('allows authenticated user to signup without registration fields', function () {
        $userModel = config('auth.providers.users.model');
        $userModel::unguard();
        $user = $userModel::create([
            'first_name' => 'Auth',
            'last_name' => 'User',
            'email' => 'authuser@example.com',
            'password' => bcrypt('password'),
        ]);
        $userModel::reguard();

        // Create customer for the user
        $customer = \Lunar\Models\Customer::create([
            'first_name' => 'Auth',
            'last_name' => 'User',
        ]);
        $customer->users()->attach($user);

        $this->actingAs($user);

        // Signup without any registration fields - should still work
        livewire(SignupPage::class)
            ->set('selectedTier', $this->tier->id)
            ->set('selectedPlan', $this->plan->id)
            ->set('paymentType', 'direct-debit')
            ->set('directDebitOwnerName', 'Account Holder')
            ->set('directDebitBankName', 'My Bank')
            ->set('directDebitIban', 'ES9121000418450200051332')
            ->set('privacy_policy', true)
            ->set('billing.first_name', 'Auth')
            ->set('billing.last_name', 'User')
            ->set('billing.contact_email', 'authuser@example.com')
            ->set('billing.country_id', $this->country->id)
            ->set('billing.state', 'Madrid')
            ->set('billing.postcode', '28001')
            ->set('billing.city', 'Madrid')
            ->set('billing.line_one', 'Calle Principal 456')
            ->call('signup')
            ->assertHasNoErrors()
            ->assertRedirectContains('checkout/procesar-pago');
    });
});

describe('SignupPage login redirect', function () {
    it('redirects to login with intended URL when redirectToLogin is called', function () {
        livewire(SignupPage::class)
            ->call('redirectToLogin')
            ->assertRedirect(route('login'));

        expect(session()->get('url.intended'))->toBe(route('testa.storefront.membership.signup'));
    });
});

describe('SignupPage validation', function () {
    it('validates tier is required', function () {
        livewire(SignupPage::class)
            ->set('selectedTier', null)
            ->call('signup')
            ->assertHasErrors(['selectedTier']);
    });

    it('validates plan is required', function () {
        livewire(SignupPage::class)
            ->set('selectedTier', $this->tier->id)
            ->set('selectedPlan', null)
            ->call('signup')
            ->assertHasErrors(['selectedPlan']);
    });

    it('validates payment type is required', function () {
        livewire(SignupPage::class)
            ->set('selectedTier', $this->tier->id)
            ->set('selectedPlan', $this->plan->id)
            ->set('paymentType', null)
            ->call('signup')
            ->assertHasErrors(['paymentType']);
    });

    it('validates direct debit fields when direct-debit payment type is selected', function () {
        livewire(SignupPage::class)
            ->set('selectedTier', $this->tier->id)
            ->set('selectedPlan', $this->plan->id)
            ->set('paymentType', 'direct-debit')
            ->set('directDebitOwnerName', null)
            ->set('directDebitBankName', null)
            ->set('directDebitIban', null)
            ->call('signup')
            ->assertHasErrors(['directDebitOwnerName', 'directDebitBankName', 'directDebitIban']);
    });

    it('validates IBAN format', function () {
        livewire(SignupPage::class)
            ->set('selectedTier', $this->tier->id)
            ->set('selectedPlan', $this->plan->id)
            ->set('paymentType', 'direct-debit')
            ->set('directDebitOwnerName', 'Test Owner')
            ->set('directDebitBankName', 'Test Bank')
            ->set('directDebitIban', 'INVALID_IBAN')
            ->call('signup')
            ->assertHasErrors(['directDebitIban']);
    });

    it('validates privacy policy is accepted', function () {
        livewire(SignupPage::class)
            ->set('selectedTier', $this->tier->id)
            ->set('selectedPlan', $this->plan->id)
            ->set('paymentType', 'direct-debit')
            ->set('directDebitOwnerName', 'Test Owner')
            ->set('directDebitBankName', 'Test Bank')
            ->set('directDebitIban', 'ES9121000418450200051332')
            ->set('privacy_policy', false)
            ->call('signup')
            ->assertHasErrors(['privacy_policy']);
    });
});
