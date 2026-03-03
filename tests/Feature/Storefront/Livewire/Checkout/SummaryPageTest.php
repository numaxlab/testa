<?php

use Illuminate\Support\Facades\Schema;
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
use Lunar\Models\Url;
use Testa\Storefront\Livewire\Checkout\SummaryPage;

use function Pest\Livewire\livewire;

beforeEach(function () {
    Schema::table('users', function ($table) {
        $table->dropColumn('name');
        $table->string('first_name')->after('id');
        $table->string('last_name')->after('first_name');
    });

    config(['auth.providers.users.model' => \Testa\Tests\Stubs\User::class]);

    $this->language = Language::factory()->create(['default' => true]);
    $this->currency = Currency::factory()->create(['default' => true, 'decimal_places' => 2, 'exchange_rate' => 1]);
    $this->channel = Channel::factory()->create(['default' => true]);
    $this->taxClass = TaxClass::factory()->create(['default' => true]);
    $this->customerGroup = CustomerGroup::factory()->create(['default' => true]);

    // Geslib product type must have ID 1
    $this->geslibProductType = ProductType::factory()->create(['id' => 1]);

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

    $customer = \Lunar\Models\Customer::create([
        'first_name' => 'Test',
        'last_name' => 'User',
    ]);
    $customer->users()->attach($this->user);
});

describe('mount redirects', function () {
    it('redirects to / when no cart in session', function () {
        $this->actingAs($this->user);

        livewire(SummaryPage::class)
            ->assertRedirect('/');
    });

    it('redirects to / when cart has no lines', function () {
        $this->actingAs($this->user);

        $cart = Cart::factory()->create([
            'currency_id' => Currency::getDefault()->id,
            'channel_id' => Channel::getDefault()->id,
            'user_id' => $this->user->id,
        ]);
        CartSession::use($cart);

        livewire(SummaryPage::class)
            ->assertRedirect('/');
    });

    it('renders with empty lines when all cart items are non-geslib', function () {
        $this->actingAs($this->user);

        $otherType = ProductType::factory()->create();
        $cart = Cart::factory()->create([
            'currency_id' => Currency::getDefault()->id,
            'channel_id' => Channel::getDefault()->id,
            'user_id' => $this->user->id,
        ]);

        $product = Product::factory()->create(['product_type_id' => $otherType->id]);
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

        CartSession::use($cart->fresh(['lines']));

        $component = livewire(SummaryPage::class);

        // Non-geslib items are filtered out, resulting in empty lines
        expect($component->get('lines'))->toBeEmpty();
    });
});

describe('mount with valid cart', function () {
    it('loads successfully with a geslib cart and maps lines', function () {
        $this->actingAs($this->user);

        $cart = Cart::factory()->create([
            'currency_id' => Currency::getDefault()->id,
            'channel_id' => Channel::getDefault()->id,
            'user_id' => $this->user->id,
        ]);

        $product = Product::factory()->create(['product_type_id' => 1]);
        Url::factory()->create([
            'slug' => 'test-product',
            'default' => true,
            'element_type' => Product::morphName(),
            'element_id' => $product->id,
            'language_id' => $this->language->id,
        ]);
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

        CartSession::use($cart->fresh(['lines']));

        $component = livewire(SummaryPage::class);

        expect($component->get('lines'))->toHaveCount(1);
        expect($component->get('lines.0.slug'))->toBe('test-product');
        expect($component->get('lines.0.quantity'))->toBe(1);
    });
});
