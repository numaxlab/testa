<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Lunar\Models\CustomerGroup;
use Lunar\Shipping\Models\ShippingMethod;
use Testa\Storefront\Queries\Checkout\GetPickupShippingMethods;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
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

    Schema::create('lunar_customer_group_shipping_method', function ($table) {
        $table->bigIncrements('id');
        $table->foreignId('customer_group_id')->constrained('lunar_customer_groups');
        $table->foreignId('shipping_method_id')->constrained('lunar_shipping_methods');
        $table->boolean('enabled')->default(false)->index();
        $table->timestamp('starts_at')->nullable()->index();
        $table->timestamp('ends_at')->nullable()->index();
        $table->boolean('visible')->default(true)->index();
        $table->timestamps();
    });

    CustomerGroup::factory()->create(['default' => true]);
});

it('returns only shipping methods with collection driver', function () {
    ShippingMethod::factory()->create(['driver' => 'collection']);
    ShippingMethod::factory()->create(['driver' => 'collection']);
    ShippingMethod::factory()->create(['driver' => 'ship-by']);

    $result = new GetPickupShippingMethods()->execute();

    expect($result)
        ->toHaveCount(2)
        ->each(fn($method) => $method->driver->toBe('collection'));
});

it('returns empty collection when no pickup methods exist', function () {
    ShippingMethod::factory()->create(['driver' => 'ship-by']);

    $result = new GetPickupShippingMethods()->execute();

    expect($result)->toBeEmpty();
});
