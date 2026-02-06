<?php

use Illuminate\Support\Facades\Schema;
use Lunar\Models\Cart;
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
use Testa\Settings\ContactSettings;
use Testa\Settings\PaymentSettings;
use Testa\Storefront\Livewire\Membership\DonatePage;

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
    $this->currency = Currency::factory()->create([
        'default' => true,
        'decimal_places' => 2,
        'exchange_rate' => 1,
    ]);
    $this->channel = Channel::factory()->create(['default' => true]);
    $this->taxClass = TaxClass::factory()->create(['default' => true]);
    $this->customerGroup = CustomerGroup::factory()->create(['default' => true]);

    $this->country = Country::factory()->create(['iso2' => 'ES']);
    $this->taxZone = TaxZone::factory()->create(['default' => true, 'zone_type' => 'country']);
    TaxZoneCountry::factory()->create([
        'tax_zone_id' => $this->taxZone->id,
        'country_id' => $this->country->id,
    ]);
    $this->taxRate = TaxRate::factory()->create(['tax_zone_id' => $this->taxZone->id]);
    TaxRateAmount::factory()->create([
        'tax_rate_id' => $this->taxRate->id,
        'tax_class_id' => $this->taxClass->id,
        'percentage' => 0, // Donations are typically tax-free
    ]);

    // Create donation product with multiple variants for preset amounts
    $this->productType = ProductType::factory()->create();
    $this->donationProduct = Product::factory()->create([
        'product_type_id' => $this->productType->id,
    ]);

    // Base donation variant (used for free amount donations)
    $this->baseDonationVariant = ProductVariant::factory()->create([
        'product_id' => $this->donationProduct->id,
        'tax_class_id' => $this->taxClass->id,
        'sku' => DonatePage::DONATION_PRODUCT_SKU,
    ]);
    Price::factory()->create([
        'priceable_type' => ProductVariant::morphName(),
        'priceable_id' => $this->baseDonationVariant->id,
        'currency_id' => $this->currency->id,
        'min_quantity' => 1,
        'price' => 0, // Base price is 0, will be set via meta
    ]);

    // Preset amount variant (e.g., 10 EUR)
    $this->presetVariant = ProductVariant::factory()->create([
        'product_id' => $this->donationProduct->id,
        'tax_class_id' => $this->taxClass->id,
        'sku' => 'donation-10',
    ]);
    Price::factory()->create([
        'priceable_type' => ProductVariant::morphName(),
        'priceable_id' => $this->presetVariant->id,
        'currency_id' => $this->currency->id,
        'min_quantity' => 1,
        'price' => 1000, // 10.00 EUR
    ]);

    // Mock ContactSettings with primary address
    $mockContactSettings = Mockery::mock(ContactSettings::class);
    $mockContactSettings->shouldReceive('getPrimaryAddress')->andReturn([
        'is_primary' => true,
        'country_iso2' => 'ES',
        'line_one' => 'Test Street 123',
        'city' => 'Madrid',
        'postcode' => '28001',
    ]);
    app()->instance(ContactSettings::class, $mockContactSettings);

    // Mock PaymentSettings
    $mockPaymentSettings = Mockery::mock(PaymentSettings::class);
    $mockPaymentSettings->donation = ['card'];
    app()->instance(PaymentSettings::class, $mockPaymentSettings);
});

describe('DonatePage free quantity', function () {
    it('validates free quantity value is required when free is selected', function () {
        $userModel = config('auth.providers.users.model');
        $userModel::unguard();
        $user = $userModel::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'),
        ]);
        $userModel::reguard();

        $customer = \Lunar\Models\Customer::create([
            'first_name' => 'Test',
            'last_name' => 'User',
        ]);
        $customer->users()->attach($user);

        $this->actingAs($user);

        livewire(DonatePage::class)
            ->set('selectedQuantity', 'free')
            ->set('freeQuantityValue', null)
            ->set('paymentType', 'card')
            ->set('privacy_policy', true)
            ->call('donate')
            ->assertHasErrors(['freeQuantityValue']);
    });

    it('validates free quantity value must be at least 1', function () {
        $userModel = config('auth.providers.users.model');
        $userModel::unguard();
        $user = $userModel::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'),
        ]);
        $userModel::reguard();

        $customer = \Lunar\Models\Customer::create([
            'first_name' => 'Test',
            'last_name' => 'User',
        ]);
        $customer->users()->attach($user);

        $this->actingAs($user);

        livewire(DonatePage::class)
            ->set('selectedQuantity', 'free')
            ->set('freeQuantityValue', 0)
            ->set('paymentType', 'card')
            ->set('privacy_policy', true)
            ->call('donate')
            ->assertHasErrors(['freeQuantityValue']);
    });

    it('creates cart with custom unit price when free quantity is selected', function () {
        $userModel = config('auth.providers.users.model');
        $userModel::unguard();
        $user = $userModel::create([
            'first_name' => 'Test',
            'last_name' => 'Donor',
            'email' => 'donor@example.com',
            'password' => bcrypt('password'),
        ]);
        $userModel::reguard();

        $customer = \Lunar\Models\Customer::create([
            'first_name' => 'Test',
            'last_name' => 'Donor',
        ]);
        $customer->users()->attach($user);

        $this->actingAs($user);

        livewire(DonatePage::class)
            ->set('selectedQuantity', 'free')
            ->set('freeQuantityValue', 25) // 25 EUR
            ->set('paymentType', 'card')
            ->set('privacy_policy', true)
            ->call('donate')
            ->assertHasNoErrors()
            ->assertRedirectContains('checkout/procesar-pago');

        // Verify cart was created with custom unit price
        $cart = Cart::latest()->first();
        expect($cart)->not->toBeNull();
        expect($cart->lines)->toHaveCount(1);

        $line = $cart->lines->first();
        expect($line->meta['unit_price'])->toBe(2500); // 25 EUR in cents
    });

    it('creates cart with preset variant price when preset quantity is selected', function () {
        $userModel = config('auth.providers.users.model');
        $userModel::unguard();
        $user = $userModel::create([
            'first_name' => 'Test',
            'last_name' => 'Donor',
            'email' => 'donor@example.com',
            'password' => bcrypt('password'),
        ]);
        $userModel::reguard();

        $customer = \Lunar\Models\Customer::create([
            'first_name' => 'Test',
            'last_name' => 'Donor',
        ]);
        $customer->users()->attach($user);

        $this->actingAs($user);

        livewire(DonatePage::class)
            ->set('selectedQuantity', $this->presetVariant->id)
            ->set('paymentType', 'card')
            ->set('privacy_policy', true)
            ->call('donate')
            ->assertHasNoErrors()
            ->assertRedirectContains('checkout/procesar-pago');

        // Verify cart was created with the preset variant
        $cart = Cart::latest()->first();
        expect($cart)->not->toBeNull();
        expect($cart->lines)->toHaveCount(1);

        $line = $cart->lines->first();
        expect($line->purchasable_id)->toBe($this->presetVariant->id);
        expect($line->meta)->not->toHaveKey('unit_price');
    });
});

describe('DonatePage for guests', function () {
    it('requires registration fields for guests', function () {
        livewire(DonatePage::class)
            ->set('selectedQuantity', 'free')
            ->set('freeQuantityValue', 10)
            ->set('paymentType', 'card')
            ->set('privacy_policy', true)
            ->call('donate')
            ->assertHasErrors(['first_name', 'last_name', 'email', 'password']);
    });

    it('creates user and donates when guest provides valid data', function () {
        $userModel = config('auth.providers.users.model');

        livewire(DonatePage::class)
            ->set('selectedQuantity', 'free')
            ->set('freeQuantityValue', 50)
            ->set('paymentType', 'card')
            ->set('privacy_policy', true)
            ->set('first_name', 'New')
            ->set('last_name', 'Donor')
            ->set('email', 'newdonor@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('donate')
            ->assertHasNoErrors()
            ->assertRedirectContains('checkout/procesar-pago');

        // Verify user was created
        expect($userModel::where('email', 'newdonor@example.com')->exists())->toBeTrue();

        // Verify user is logged in
        expect(auth()->check())->toBeTrue();

        // Verify cart was created with custom price
        $cart = Cart::latest()->first();
        expect($cart->lines->first()->meta['unit_price'])->toBe(5000);
    });
});

describe('DonatePage validation', function () {
    it('validates quantity selection is required', function () {
        livewire(DonatePage::class)
            ->set('selectedQuantity', null)
            ->call('donate')
            ->assertHasErrors(['selectedQuantity']);
    });

    it('validates payment type is required', function () {
        $userModel = config('auth.providers.users.model');
        $userModel::unguard();
        $user = $userModel::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'),
        ]);
        $userModel::reguard();

        $customer = \Lunar\Models\Customer::create([
            'first_name' => 'Test',
            'last_name' => 'User',
        ]);
        $customer->users()->attach($user);

        $this->actingAs($user);

        livewire(DonatePage::class)
            ->set('selectedQuantity', 'free')
            ->set('freeQuantityValue', 10)
            ->set('paymentType', '')
            ->set('privacy_policy', true)
            ->call('donate')
            ->assertHasErrors(['paymentType']);
    });

    it('validates privacy policy is accepted', function () {
        livewire(DonatePage::class)
            ->set('selectedQuantity', 'free')
            ->set('freeQuantityValue', 10)
            ->set('paymentType', 'card')
            ->set('privacy_policy', false)
            ->call('donate')
            ->assertHasErrors(['privacy_policy']);
    });
});

describe('DonatePage login redirect', function () {
    it('redirects to login with intended URL', function () {
        livewire(DonatePage::class)
            ->call('redirectToLogin')
            ->assertRedirect(route('login'));

        expect(session()->get('url.intended'))->toBe(route('testa.storefront.membership.donate'));
    });
});
