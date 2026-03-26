<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;
use Testa\Models\Education\CourseModule;
use Testa\Models\News\Event;
use Testa\Observers\CourseObserver;
use Testa\Storefront\Queries\News\GetProductActivities;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);
    Channel::factory()->create(['default' => true]);
    CustomerGroup::factory()->create(['default' => true]);

    $productOption = ProductOption::factory()->create([
        'handle' => CourseObserver::RATE_PRODUCT_OPTION_HANDLE,
    ]);
    ProductOptionValue::factory()->create([
        'product_option_id' => $productOption->id,
    ]);

    $productType = ProductType::factory()->create();
    $this->product = Product::factory()->create(['product_type_id' => $productType->id]);
});

it('returns a collection', function () {
    $result = new GetProductActivities()->execute($this->product);

    expect($result)->toBeInstanceOf(Collection::class);
});

it('returns published events linked to the product', function () {
    $event = Event::factory()->create(['is_published' => true]);
    $event->products()->attach($this->product->id, ['position' => 1]);

    $result = new GetProductActivities()->execute($this->product);

    expect($result->pluck('id')->contains($event->id))->toBeTrue();
});

it('returns published course modules linked to the product', function () {
    $module = CourseModule::factory()->create(['is_published' => true]);
    $module->products()->attach($this->product->id, ['position' => 1]);

    $result = new GetProductActivities()->execute($this->product);

    expect($result->pluck('id')->contains($module->id))->toBeTrue();
});

it('excludes unpublished events', function () {
    $event = Event::factory()->create(['is_published' => false]);
    $event->products()->attach($this->product->id, ['position' => 1]);

    $result = new GetProductActivities()->execute($this->product);

    expect($result)->toBeEmpty();
});

it('excludes unpublished course modules', function () {
    $module = CourseModule::factory()->create(['is_published' => false]);
    $module->products()->attach($this->product->id, ['position' => 1]);

    $result = new GetProductActivities()->execute($this->product);

    expect($result)->toBeEmpty();
});

it('does not return activities linked to other products', function () {
    $otherProduct = Product::factory()->create(['product_type_id' => $this->product->product_type_id]);
    $event = Event::factory()->create(['is_published' => true]);
    $event->products()->attach($otherProduct->id, ['position' => 1]);

    $result = new GetProductActivities()->execute($this->product);

    expect($result)->toBeEmpty();
});

it('returns an empty collection when product has no activities', function () {
    $result = new GetProductActivities()->execute($this->product);

    expect($result)->toBeEmpty();
});
