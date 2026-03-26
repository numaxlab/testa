<?php

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
use Testa\Models\Education\Course;
use Testa\Models\Education\CourseModule;
use Testa\Observers\CourseObserver;
use Testa\Storefront\Queries\Education\GetModuleProducts;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create(['default' => true]);
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);
    Channel::factory()->create(['default' => true]);
    CustomerGroup::factory()->create(['default' => true]);

    $productOption = ProductOption::factory()->create([
        'handle' => CourseObserver::RATE_PRODUCT_OPTION_HANDLE,
    ]);
    ProductOptionValue::factory()->create(['product_option_id' => $productOption->id]);

    $productType = ProductType::factory()->create();
    config(['lunar.geslib.product_type_id' => $productType->id]);
    $this->productType = $productType;

    $this->course = Course::factory()->create();
    $this->module = CourseModule::factory()->create(['course_id' => $this->course->id]);
});

it('returns products linked directly to the module', function () {
    $product = Product::factory()->create(['product_type_id' => $this->productType->id, 'status' => 'published']);
    $this->module->products()->attach($product->id, ['position' => 1]);

    $result = new GetModuleProducts()->execute($this->module);

    expect($result->pluck('id')->contains($product->id))->toBeTrue();
});

it('returns products linked to the parent course', function () {
    $product = Product::factory()->create(['product_type_id' => $this->productType->id, 'status' => 'published']);
    $this->course->products()->attach($product->id, ['position' => 1]);

    $result = new GetModuleProducts()->execute($this->module->fresh());

    expect($result->pluck('id')->contains($product->id))->toBeTrue();
});

it('merges products from module and parent course', function () {
    $moduleProduct = Product::factory()->create(['product_type_id' => $this->productType->id, 'status' => 'published']);
    $courseProduct = Product::factory()->create(['product_type_id' => $this->productType->id, 'status' => 'published']);
    $this->module->products()->attach($moduleProduct->id, ['position' => 1]);
    $this->course->products()->attach($courseProduct->id, ['position' => 1]);

    $result = new GetModuleProducts()->execute($this->module->fresh());

    expect($result->pluck('id')->contains($moduleProduct->id))
        ->toBeTrue()
        ->and($result->pluck('id')->contains($courseProduct->id))->toBeTrue();
});

it('does not return unpublished products', function () {
    $product = Product::factory()->create(['product_type_id' => $this->productType->id, 'status' => 'draft']);
    $this->module->products()->attach($product->id, ['position' => 1]);

    $result = new GetModuleProducts()->execute($this->module);

    expect($result)->toBeEmpty();
});

it('returns an empty collection when module and course have no products', function () {
    $result = new GetModuleProducts()->execute($this->module);

    expect($result)->toBeEmpty();
});
