<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Cart;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Storefront\UseCases\Checkout\SaveCouponCode;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create(['default' => true]);
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true, 'decimal_places' => 2]);
    Channel::factory()->create(['default' => true]);
    CustomerGroup::factory()->create(['default' => true]);

    $this->cart = Cart::factory()->create([
        'currency_id' => Currency::getDefault()->id,
        'channel_id' => Channel::getDefault()->id,
    ]);
});

it('sets the coupon code on the cart', function () {
    new SaveCouponCode()->execute($this->cart, 'PROMO10');

    expect($this->cart->fresh()->coupon_code)->toBe('PROMO10');
});

it('clears the coupon code when null is passed', function () {
    $this->cart->coupon_code = 'PROMO10';
    $this->cart->save();

    new SaveCouponCode()->execute($this->cart, null);

    expect($this->cart->fresh()->coupon_code)->toBeNull();
});
