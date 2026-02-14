<?php

use Illuminate\Support\Facades\Schema;
use Lunar\Models\Cart;
use Lunar\Models\CartAddress;
use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Order;
use Lunar\Models\Price;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Lunar\Models\TaxRate;
use Lunar\Models\TaxRateAmount;
use Lunar\Models\TaxZone;
use Lunar\Models\TaxZoneCountry;
use Testa\Contracts\Payment\PaymentGatewayAdapter;
use Testa\Contracts\Payment\PaymentResult;
use Testa\Contracts\Payment\PaymentResultType;
use Testa\Payment\PaymentGatewayRegistry;

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

    $this->customer = \Lunar\Models\Customer::factory()->create();
    $this->customer->users()->attach($this->user);

    $this->productType = ProductType::factory()->create();
    $this->product = Product::factory()->create([
        'product_type_id' => $this->productType->id,
    ]);
    $this->variant = ProductVariant::factory()->create([
        'product_id' => $this->product->id,
        'tax_class_id' => $this->taxClass->id,
        'purchasable' => 'always',
        'shippable' => false,
    ]);
    Price::factory()->create([
        'priceable_type' => ProductVariant::morphName(),
        'priceable_id' => $this->variant->id,
        'currency_id' => $this->currency->id,
        'min_quantity' => 1,
        'price' => 1000,
    ]);
});

function createCartWithBilling($user, $currency, $channel, $country, $variant, array $meta = []): Cart
{
    $cart = Cart::create(array_filter([
        'user_id' => $user->id,
        'currency_id' => $currency->id,
        'channel_id' => $channel->id,
        'meta' => $meta ?: null,
    ]));
    $cart->add($variant, 1);

    $billing = new CartAddress;
    $billing->first_name = 'Test';
    $billing->last_name = 'User';
    $billing->country_id = $country->id;
    $billing->city = 'Madrid';
    $billing->postcode = '28001';
    $billing->line_one = 'Test Street 1';
    $cart->setBillingAddress($billing);

    $cart->calculate();

    return $cart;
}

describe('ProcessPaymentController', function () {
    it('returns 403 when user does not own the cart', function () {
        $otherUserModel = config('auth.providers.users.model');
        $otherUserModel::unguard();
        $otherUser = $otherUserModel::create([
            'first_name' => 'Other',
            'last_name' => 'User',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
        ]);
        $otherUserModel::reguard();

        $cart = createCartWithBilling(
            $this->user, $this->currency, $this->channel, $this->country, $this->variant
        );

        $this->actingAs($otherUser)
            ->get(route('testa.storefront.checkout.process-payment', [
                'id' => $cart->id,
                'fingerprint' => $cart->fingerprint(),
                'payment' => 'card',
            ]))
            ->assertForbidden();
    });

    it('redirects with error when fingerprint does not match', function () {
        $cart = createCartWithBilling(
            $this->user, $this->currency, $this->channel, $this->country, $this->variant
        );

        $this->actingAs($this->user)
            ->get(route('testa.storefront.checkout.process-payment', [
                'id' => $cart->id,
                'fingerprint' => 'invalid-fingerprint',
                'payment' => 'card',
            ]))
            ->assertRedirect()
            ->assertSessionHasErrors('fingerprint');
    });

    it('redirects with error when payment type adapter is not found', function () {
        config(['lunar.payments.types.unknown_type.driver' => 'nonexistent-driver']);

        $cart = createCartWithBilling(
            $this->user, $this->currency, $this->channel, $this->country, $this->variant
        );

        $this->actingAs($this->user)
            ->get(route('testa.storefront.checkout.process-payment', [
                'id' => $cart->id,
                'fingerprint' => $cart->fingerprint(),
                'payment' => 'unknown_type',
            ]))
            ->assertRedirect()
            ->assertSessionHasErrors('payment');
    });

    it('returns 404 when cart does not exist', function () {
        $this->actingAs($this->user)
            ->get(route('testa.storefront.checkout.process-payment', [
                'id' => 99999,
                'fingerprint' => 'irrelevant',
                'payment' => 'card',
            ]))
            ->assertNotFound();
    });

    it('requires authentication', function () {
        $cart = Cart::create([
            'user_id' => $this->user->id,
            'currency_id' => $this->currency->id,
            'channel_id' => $this->channel->id,
        ]);

        $this->get(route('testa.storefront.checkout.process-payment', [
            'id' => $cart->id,
            'fingerprint' => 'test',
            'payment' => 'card',
        ]))
            ->assertRedirect();
    });
});

describe('ProcessPaymentController route mapping', function () {
    it('redirects to correct checkout route on fingerprint mismatch for bookshop order', function () {
        $cart = createCartWithBilling(
            $this->user, $this->currency, $this->channel, $this->country, $this->variant,
            ['Tipo de pedido' => 'Pedido librería']
        );

        $this->actingAs($this->user)
            ->get(route('testa.storefront.checkout.process-payment', [
                'id' => $cart->id,
                'fingerprint' => 'wrong-fingerprint',
                'payment' => 'card',
            ]))
            ->assertRedirect(route('testa.storefront.checkout.shipping-and-payment'))
            ->assertSessionHasErrors('fingerprint');
    });

    it('redirects to donation checkout route on fingerprint mismatch for donation order', function () {
        $cart = createCartWithBilling(
            $this->user, $this->currency, $this->channel, $this->country, $this->variant,
            ['Tipo de pedido' => 'Donación']
        );

        $this->actingAs($this->user)
            ->get(route('testa.storefront.checkout.process-payment', [
                'id' => $cart->id,
                'fingerprint' => 'wrong-fingerprint',
                'payment' => 'card',
            ]))
            ->assertRedirect(route('testa.storefront.membership.donate'))
            ->assertSessionHasErrors('fingerprint');
    });
});
