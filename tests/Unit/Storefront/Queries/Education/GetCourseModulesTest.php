<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\TaxClass;
use Testa\Models\Education\Course;
use Testa\Models\Education\CourseModule;
use Testa\Observers\CourseObserver;
use Testa\Storefront\Queries\Education\GetCourseModules;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create(['default' => true]);
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);

    $productOption = ProductOption::factory()->create([
        'handle' => CourseObserver::RATE_PRODUCT_OPTION_HANDLE,
    ]);
    ProductOptionValue::factory()->create(['product_option_id' => $productOption->id]);

    $this->course = Course::factory()->create();
});

it('returns published modules for the course', function () {
    CourseModule::factory()->count(3)->create([
        'course_id' => $this->course->id,
        'is_published' => true,
    ]);

    $result = new GetCourseModules()->execute($this->course);

    expect($result)->toHaveCount(3);
});

it('excludes unpublished modules', function () {
    CourseModule::factory()->create([
        'course_id' => $this->course->id,
        'is_published' => false,
    ]);

    $result = new GetCourseModules()->execute($this->course);

    expect($result)->toBeEmpty();
});

it('does not return modules from other courses', function () {
    $otherCourse = Course::factory()->create();
    CourseModule::factory()->create([
        'course_id' => $otherCourse->id,
        'is_published' => true,
    ]);

    $result = new GetCourseModules()->execute($this->course);

    expect($result)->toBeEmpty();
});

it('excludes the given except module', function () {
    $excluded = CourseModule::factory()->create([
        'course_id' => $this->course->id,
        'is_published' => true,
    ]);
    $included = CourseModule::factory()->create([
        'course_id' => $this->course->id,
        'is_published' => true,
    ]);

    $result = new GetCourseModules()->execute($this->course, $excluded);

    expect($result)
        ->toHaveCount(1)
        ->and($result->first()->id)->toBe($included->id);
});

it('returns all modules when except is null', function () {
    CourseModule::factory()->count(2)->create([
        'course_id' => $this->course->id,
        'is_published' => true,
    ]);

    $result = new GetCourseModules()->execute($this->course, null);

    expect($result)->toHaveCount(2);
});

it('returns modules ordered by starts_at', function () {
    CourseModule::factory()->create([
        'course_id' => $this->course->id,
        'is_published' => true,
        'starts_at' => now()->addDays(3),
    ]);
    CourseModule::factory()->create([
        'course_id' => $this->course->id,
        'is_published' => true,
        'starts_at' => now()->addDay(),
    ]);
    CourseModule::factory()->create([
        'course_id' => $this->course->id,
        'is_published' => true,
        'starts_at' => now()->addDays(2),
    ]);

    $result = new GetCourseModules()->execute($this->course);

    expect($result->pluck('starts_at')->map(fn($d) => $d->timestamp)->values()->all())
        ->toBe($result->pluck('starts_at')->map(fn($d) => $d->timestamp)->sort()->values()->all());
});

it('eager loads defaultUrl and course relations', function () {
    CourseModule::factory()->create([
        'course_id' => $this->course->id,
        'is_published' => true,
    ]);

    $result = new GetCourseModules()->execute($this->course);

    expect($result->first()->relationLoaded('defaultUrl'))
        ->toBeTrue()
        ->and($result->first()->relationLoaded('course'))->toBeTrue();
});

it('returns an empty collection when course has no modules', function () {
    $result = new GetCourseModules()->execute($this->course);

    expect($result)->toBeInstanceOf(Collection::class)->toBeEmpty();
});
