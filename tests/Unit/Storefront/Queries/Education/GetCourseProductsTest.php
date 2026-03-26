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
use Testa\Storefront\Queries\Education\GetCourseProducts;
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
});

it('returns products directly linked to the course', function () {
    $product = Product::factory()->create(['product_type_id' => $this->productType->id, 'status' => 'published']);
    $this->course->products()->attach($product->id, ['position' => 1]);

    $result = new GetCourseProducts()->execute($this->course);

    expect($result->pluck('id')->contains($product->id))->toBeTrue();
});

it('returns products linked to course modules', function () {
    $module = CourseModule::factory()->create(['course_id' => $this->course->id]);
    $product = Product::factory()->create(['product_type_id' => $this->productType->id, 'status' => 'published']);
    $module->products()->attach($product->id, ['position' => 1]);

    $result = new GetCourseProducts()->execute($this->course->fresh());

    expect($result->pluck('id')->contains($product->id))->toBeTrue();
});

it('merges products from course and all its modules', function () {
    $courseProduct = Product::factory()->create(['product_type_id' => $this->productType->id, 'status' => 'published']);
    $this->course->products()->attach($courseProduct->id, ['position' => 1]);

    $module = CourseModule::factory()->create(['course_id' => $this->course->id]);
    $moduleProduct = Product::factory()->create(['product_type_id' => $this->productType->id, 'status' => 'published']);
    $module->products()->attach($moduleProduct->id, ['position' => 1]);

    $result = new GetCourseProducts()->execute($this->course->fresh());

    expect($result->pluck('id')->contains($courseProduct->id))
        ->toBeTrue()
        ->and($result->pluck('id')->contains($moduleProduct->id))->toBeTrue();
});

it('does not return unpublished products', function () {
    $product = Product::factory()->create(['product_type_id' => $this->productType->id, 'status' => 'draft']);
    $this->course->products()->attach($product->id, ['position' => 1]);

    $result = new GetCourseProducts()->execute($this->course);

    expect($result)->toBeEmpty();
});

it('returns an empty collection when course has no products or modules', function () {
    $result = new GetCourseProducts()->execute($this->course);

    expect($result)->toBeEmpty();
});
