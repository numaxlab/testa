<?php

use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Price;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Lunar\Models\TaxRate;
use Lunar\Models\TaxRateAmount;
use Lunar\Models\TaxZone;
use Lunar\Models\TaxZoneCountry;
use Testa\Storefront\Livewire\Components\Price as PriceComponent;

beforeEach(function () {
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
});

function createPurchasableWithPrice(int $priceValue = 1000): ProductVariant
{
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'tax_class_id' => TaxClass::getDefault()->id,
    ]);

    Price::factory()->create([
        'priceable_type' => ProductVariant::morphName(),
        'priceable_id' => $variant->id,
        'currency_id' => Currency::getDefault()->id,
        'min_quantity' => 1,
        'price' => $priceValue,
    ]);

    return $variant;
}

describe('Price component', function () {
    it('renders price for purchasable item', function () {
        $variant = createPurchasableWithPrice(1000);

        $component = new PriceComponent();
        $component->purchasable = $variant;
        $result = $component->render();

        expect($result)->toBeString();
        expect($component->price)->not->toBeNull();
    });

    it('formats price with tax included', function () {
        $variant = createPurchasableWithPrice(1000); // 10.00 EUR base price

        $component = new PriceComponent();
        $component->purchasable = $variant;
        $component->render();

        // Price should include 21% tax: 10.00 * 1.21 = 12.10
        expect($component->price)->toContain('12');
    });

    it('renders inline blade template', function () {
        $variant = createPurchasableWithPrice(1000);

        $component = new PriceComponent();
        $component->purchasable = $variant;
        $result = $component->render();

        expect($result)->toContain('<span>');
        expect($result)->toContain('</span>');
        expect($result)->toContain('$price');
    });

    it('stores formatted price in price property', function () {
        $variant = createPurchasableWithPrice(2500); // 25.00 EUR

        $component = new PriceComponent();
        $component->purchasable = $variant;
        $component->render();

        // Price should be formatted as a string with currency symbol
        expect($component->price)->toBeString();
        expect($component->price)->not->toBeEmpty();
    });

    it('initializes with null purchasable', function () {
        $component = new PriceComponent();

        expect($component->purchasable)->toBeNull();
        expect($component->price)->toBeNull();
    });

    it('uses default currency for pricing', function () {
        // Create a second non-default currency
        $otherCurrency = Currency::factory()->create(['default' => false, 'code' => 'USD']);

        $variant = createPurchasableWithPrice(1000);

        // Add a price in the other currency
        Price::factory()->create([
            'priceable_type' => ProductVariant::morphName(),
            'priceable_id' => $variant->id,
            'currency_id' => $otherCurrency->id,
            'min_quantity' => 1,
            'price' => 5000, // Different price
        ]);

        $component = new PriceComponent();
        $component->purchasable = $variant;
        $component->render();

        // Should use the default currency price (1000 = 10.00 + tax)
        // Not the USD price (5000 = 50.00)
        expect($component->price)->toContain('12'); // 10.00 * 1.21 = 12.10
    });
});
