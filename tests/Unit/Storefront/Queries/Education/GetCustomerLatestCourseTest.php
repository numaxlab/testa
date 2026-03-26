<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Customer as LunarCustomer;
use Lunar\Models\Language;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\TaxClass;
use Testa\Models\Customer;
use Testa\Models\Education\Course;
use Testa\Observers\CourseObserver;
use Testa\Storefront\Queries\Education\GetCustomerLatestCourse;
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

it('returns the latest published course for the customer', function () {
    $older = Course::factory()->create(['is_published' => true, 'created_at' => now()->subDay()]);
    $newer = Course::factory()->create(['is_published' => true, 'created_at' => now()]);
    $this->customer->courses()->attach([$older->id, $newer->id]);

    $result = (new GetCustomerLatestCourse())->execute($this->customer);

    expect($result->id)->toBe($newer->id);
});

it('returns null when the customer has no courses', function () {
    $result = (new GetCustomerLatestCourse())->execute($this->customer);

    expect($result)->toBeNull();
});

it('does not return unpublished courses', function () {
    $course = Course::factory()->create(['is_published' => false]);
    $this->customer->courses()->attach($course->id);

    $result = (new GetCustomerLatestCourse())->execute($this->customer);

    expect($result)->toBeNull();
});

it('does not return courses belonging to other customers', function () {
    $otherCustomer = Customer::find(LunarCustomer::factory()->create()->id);
    $course = Course::factory()->create(['is_published' => true]);
    $otherCustomer->courses()->attach($course->id);

    $result = (new GetCustomerLatestCourse())->execute($this->customer);

    expect($result)->toBeNull();
});
