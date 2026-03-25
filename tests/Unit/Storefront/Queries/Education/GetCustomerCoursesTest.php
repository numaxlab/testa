<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Lunar\Models\Currency;
use Lunar\Models\Customer as LunarCustomer;
use Lunar\Models\Language;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\TaxClass;
use Testa\Models\Customer;
use Testa\Models\Education\Course;
use Testa\Observers\CourseObserver;
use Testa\Storefront\Queries\Education\GetCustomerCourses;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);

    $productOption = ProductOption::factory()->create([
        'handle' => CourseObserver::RATE_PRODUCT_OPTION_HANDLE,
    ]);
    ProductOptionValue::factory()->create([
        'product_option_id' => $productOption->id,
    ]);

    $this->customer = Customer::find(LunarCustomer::factory()->create()->id);
});

it('returns a paginator', function () {
    $result = (new GetCustomerCourses())->execute($this->customer);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
});

it('only returns published courses', function () {
    $published = Course::factory()->create(['is_published' => true]);
    $unpublished = Course::factory()->create(['is_published' => false]);

    $this->customer->courses()->attach([$published->id, $unpublished->id]);

    $result = (new GetCustomerCourses())->execute($this->customer);

    expect($result->total())
        ->toBe(1)
        ->and($result->items()[0]->id)->toBe($published->id);
});

it('does not return courses from other customers', function () {
    $otherCustomer = Customer::find(LunarCustomer::factory()->create()->id);
    $course = Course::factory()->create(['is_published' => true]);
    $otherCustomer->courses()->attach($course->id);

    $result = (new GetCustomerCourses())->execute($this->customer);

    expect($result->total())->toBe(0);
});

it('eager loads horizontalImage and verticalImage', function () {
    $course = Course::factory()->create(['is_published' => true]);
    $this->customer->courses()->attach($course->id);

    $result = (new GetCustomerCourses())->execute($this->customer);

    $item = $result->items()[0];
    expect($item->relationLoaded('horizontalImage'))
        ->toBeTrue()
        ->and($item->relationLoaded('verticalImage'))->toBeTrue();
});

it('paginates with the given perPage value', function () {
    $courses = Course::factory()->count(10)->create(['is_published' => true]);
    $this->customer->courses()->attach($courses->pluck('id'));

    $result = (new GetCustomerCourses())->execute($this->customer, 3);

    expect($result->perPage())
        ->toBe(3)
        ->and($result->total())->toBe(10);
});

it('defaults to 6 items per page', function () {
    $result = (new GetCustomerCourses())->execute($this->customer);

    expect($result->perPage())->toBe(6);
});
