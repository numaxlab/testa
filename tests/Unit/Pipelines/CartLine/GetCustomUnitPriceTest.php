<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Cart;
use Lunar\Models\CartLine;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\Price;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Testa\Pipelines\CartLine\GetCustomUnitPrice;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->language = Language::factory()->create(['default' => true]);
    $this->currency = Currency::factory()->create([
        'default' => true,
        'decimal_places' => 2,
        'exchange_rate' => 1,
    ]);
    $this->channel = Channel::factory()->create(['default' => true]);
    $this->taxClass = TaxClass::factory()->create(['default' => true]);

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
        'price' => 1000, // Default price 10.00
    ]);

    $this->cart = Cart::create([
        'currency_id' => $this->currency->id,
        'channel_id' => $this->channel->id,
    ]);
});

describe('GetCustomUnitPrice pipeline', function () {
    it('sets custom unit price from meta', function () {
        $cartLine = CartLine::create([
            'cart_id' => $this->cart->id,
            'purchasable_type' => ProductVariant::morphName(),
            'purchasable_id' => $this->variant->id,
            'quantity' => 1,
            'meta' => ['unit_price' => 2500], // 25.00 EUR
        ]);

        $cartLine->load(['cart.currency', 'purchasable']);

        $pipeline = new GetCustomUnitPrice();
        $result = $pipeline->handle($cartLine, fn($line) => $line);

        expect($result->unitPrice)->not->toBeNull();
        expect($result->unitPrice->value)->toBe(2500);
        expect($result->unitPriceInclTax)->not->toBeNull();
        expect($result->unitPriceInclTax->value)->toBe(2500);
    });

    it('does not set unit price when meta has no unit_price', function () {
        $cartLine = CartLine::create([
            'cart_id' => $this->cart->id,
            'purchasable_type' => ProductVariant::morphName(),
            'purchasable_id' => $this->variant->id,
            'quantity' => 1,
            'meta' => ['other_key' => 'other_value'],
        ]);

        $cartLine->load(['cart.currency', 'purchasable']);

        $pipeline = new GetCustomUnitPrice();
        $result = $pipeline->handle($cartLine, fn($line) => $line);

        // Unit price should not be set by this pipeline
        expect($result->unitPrice)->toBeNull();
    });

    it('handles zero unit price', function () {
        $cartLine = CartLine::create([
            'cart_id' => $this->cart->id,
            'purchasable_type' => ProductVariant::morphName(),
            'purchasable_id' => $this->variant->id,
            'quantity' => 1,
            'meta' => ['unit_price' => 0],
        ]);

        $cartLine->load(['cart.currency', 'purchasable']);

        $pipeline = new GetCustomUnitPrice();
        $result = $pipeline->handle($cartLine, fn($line) => $line);

        expect($result->unitPrice->value)->toBe(0);
    });

    it('converts float unit price to integer', function () {
        $cartLine = CartLine::create([
            'cart_id' => $this->cart->id,
            'purchasable_type' => ProductVariant::morphName(),
            'purchasable_id' => $this->variant->id,
            'quantity' => 1,
            'meta' => ['unit_price' => 25.5], // Should become 25
        ]);

        $cartLine->load(['cart.currency', 'purchasable']);

        $pipeline = new GetCustomUnitPrice();
        $result = $pipeline->handle($cartLine, fn($line) => $line);

        expect($result->unitPrice->value)->toBe(25);
    });
});
