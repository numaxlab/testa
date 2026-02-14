<?php

use Lunar\Base\ValueObjects\Cart\TaxBreakdown;
use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Order;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\ProductVariant;
use Lunar\Models\Tag;
use Lunar\Models\TaxClass;
use Lunar\Models\TaxRate;
use Lunar\Models\TaxRateAmount;
use Lunar\Models\TaxZone;
use Lunar\Models\TaxZoneCountry;
use Testa\Observers\CourseObserver;
use Testa\Observers\MembershipTierObserver;
use Testa\Pipelines\Order\Creation\TagOrder;
use Testa\Storefront\Livewire\Membership\DonatePage;

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

    $this->pipeline = new TagOrder();
});

describe('TagOrder pipeline', function () {
    it('tags order as membership subscription when line has membership product type', function () {
        $product = Product::factory()->create([
            'product_type_id' => MembershipTierObserver::PRODUCT_TYPE_ID,
        ]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'tax_class_id' => $this->taxClass->id,
            'sku' => 'membership-test',
        ]);

        $order = Order::factory()->create([
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);
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

        $nextCalled = false;
        $this->pipeline->handle($order, function ($order) use (&$nextCalled) {
            $nextCalled = true;
            return $order;
        });

        expect($nextCalled)->toBeTrue();
        expect($order->tags->pluck('value'))->toContain('Subscripción socias');
    });

    it('tags order as donation when line has donation product SKU', function () {
        $regularProductType = ProductType::factory()->create();
        $product = Product::factory()->create([
            'product_type_id' => $regularProductType->id,
        ]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'tax_class_id' => $this->taxClass->id,
            'sku' => DonatePage::DONATION_PRODUCT_SKU,
        ]);

        $order = Order::factory()->create([
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);
        $order->lines()->create([
            'purchasable_type' => 'product_variant',
            'purchasable_id' => $variant->id,
            'type' => 'physical',
            'description' => 'Donation',
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

        $this->pipeline->handle($order, fn ($order) => $order);

        expect($order->tags->pluck('value'))->toContain('Donación');
    });

    it('tags order as course enrollment when line has course product type', function () {
        $product = Product::factory()->create([
            'product_type_id' => CourseObserver::PRODUCT_TYPE_ID,
        ]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'tax_class_id' => $this->taxClass->id,
            'sku' => 'course-1-1',
        ]);

        $order = Order::factory()->create([
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);
        $order->lines()->create([
            'purchasable_type' => 'product_variant',
            'purchasable_id' => $variant->id,
            'type' => 'physical',
            'description' => 'Course',
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

        $this->pipeline->handle($order, fn ($order) => $order);

        expect($order->tags->pluck('value'))->toContain('Inscripción cursos');
    });

    it('tags order as bookshop order when no special product type matches', function () {
        $regularProductType = ProductType::factory()->create();
        $product = Product::factory()->create([
            'product_type_id' => $regularProductType->id,
        ]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'tax_class_id' => $this->taxClass->id,
            'sku' => 'book-123',
        ]);

        $order = Order::factory()->create([
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);
        $order->lines()->create([
            'purchasable_type' => 'product_variant',
            'purchasable_id' => $variant->id,
            'type' => 'physical',
            'description' => 'A book',
            'identifier' => $variant->sku,
            'unit_price' => 2000,
            'unit_quantity' => 1,
            'quantity' => 1,
            'sub_total' => 2000,
            'discount_total' => 0,
            'tax_breakdown' => new TaxBreakdown,
            'tax_total' => 0,
            'total' => 2000,
        ]);

        $this->pipeline->handle($order, fn ($order) => $order);

        expect($order->tags->pluck('value'))->toContain('Pedido librería');
    });

    it('tags order as bookshop order when there are no lines', function () {
        $order = Order::factory()->create([
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);

        $this->pipeline->handle($order, fn ($order) => $order);

        expect($order->tags->pluck('value'))->toContain('Pedido librería');
    });

    it('membership tag takes priority over other tags', function () {
        $membershipProduct = Product::factory()->create([
            'product_type_id' => MembershipTierObserver::PRODUCT_TYPE_ID,
        ]);
        $membershipVariant = ProductVariant::factory()->create([
            'product_id' => $membershipProduct->id,
            'tax_class_id' => $this->taxClass->id,
            'sku' => 'membership-1',
        ]);

        $courseProduct = Product::factory()->create([
            'product_type_id' => CourseObserver::PRODUCT_TYPE_ID,
        ]);
        $courseVariant = ProductVariant::factory()->create([
            'product_id' => $courseProduct->id,
            'tax_class_id' => $this->taxClass->id,
            'sku' => 'course-1',
        ]);

        $order = Order::factory()->create([
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);

        // Course line first
        $order->lines()->create([
            'purchasable_type' => 'product_variant',
            'purchasable_id' => $courseVariant->id,
            'type' => 'physical',
            'description' => 'Course',
            'identifier' => $courseVariant->sku,
            'unit_price' => 3000,
            'unit_quantity' => 1,
            'quantity' => 1,
            'sub_total' => 3000,
            'discount_total' => 0,
            'tax_breakdown' => new TaxBreakdown,
            'tax_total' => 0,
            'total' => 3000,
        ]);

        // Membership line second
        $order->lines()->create([
            'purchasable_type' => 'product_variant',
            'purchasable_id' => $membershipVariant->id,
            'type' => 'physical',
            'description' => 'Membership',
            'identifier' => $membershipVariant->sku,
            'unit_price' => 5000,
            'unit_quantity' => 1,
            'quantity' => 1,
            'sub_total' => 5000,
            'discount_total' => 0,
            'tax_breakdown' => new TaxBreakdown,
            'tax_total' => 0,
            'total' => 5000,
        ]);

        $this->pipeline->handle($order, fn ($order) => $order);

        // Only one tag should be attached, and it should be membership (breaks on membership)
        expect($order->tags)->toHaveCount(1);
    });

    it('reuses existing tags instead of creating duplicates', function () {
        Tag::firstOrCreate(['value' => 'Pedido librería']);
        expect(Tag::where('value', 'Pedido librería')->count())->toBe(1);

        $regularProductType = ProductType::factory()->create();
        $product = Product::factory()->create([
            'product_type_id' => $regularProductType->id,
        ]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'tax_class_id' => $this->taxClass->id,
        ]);

        $order = Order::factory()->create([
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

        $this->pipeline->handle($order, fn ($order) => $order);

        expect(Tag::where('value', 'Pedido librería')->count())->toBe(1);
    });

    it('passes order to the next pipeline step', function () {
        $order = Order::factory()->create([
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);

        $result = $this->pipeline->handle($order, fn ($order) => 'next-called');

        expect($result)->toBe('next-called');
    });
});
