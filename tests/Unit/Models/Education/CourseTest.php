<?php

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\TaxClass;
use Testa\Models\Education\Course;
use Testa\Observers\CourseObserver;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);

    // Create the ProductOption needed by CourseObserver
    $productOption = ProductOption::factory()->create([
        'handle' => CourseObserver::RATE_PRODUCT_OPTION_HANDLE,
    ]);
    ProductOptionValue::factory()->create([
        'product_option_id' => $productOption->id,
    ]);
});

it('has modules relationship', function () {
    $course = new Course();
    expect($course->modules())->toBeInstanceOf(HasMany::class);
});

it('has topic relationship', function () {
    $course = new Course();
    expect($course->topic())->toBeInstanceOf(BelongsTo::class);
});

it('has horizontalImage relationship', function () {
    $course = new Course();
    expect($course->horizontalImage())->toBeInstanceOf(MorphOne::class);
});

it('has verticalImage relationship', function () {
    $course = new Course();
    expect($course->verticalImage())->toBeInstanceOf(MorphOne::class);
});

it('has attachments relationship', function () {
    $course = new Course();
    expect($course->attachments())->toBeInstanceOf(MorphMany::class);
});

it('has products relationship', function () {
    $course = new Course();
    expect($course->products())->toBeInstanceOf(BelongsToMany::class);
});

it('has purchasable relationship', function () {
    $course = new Course();
    expect($course->purchasable())->toBeInstanceOf(BelongsTo::class);
});

it('casts starts_at to date', function () {
    $course = Course::factory()->create();
    expect($course->starts_at)->toBeInstanceOf(Carbon::class);
});

it('casts ends_at to date', function () {
    $course = Course::factory()->create();
    expect($course->ends_at)->toBeInstanceOf(Carbon::class);
});

it('has translatable name field', function () {
    $course = new Course();
    expect($course->translatable)->toContain('name');
});

it('has translatable subtitle field', function () {
    $course = new Course();
    expect($course->translatable)->toContain('subtitle');
});

it('has translatable description field', function () {
    $course = new Course();
    expect($course->translatable)->toContain('description');
});

it('has translatable alert field', function () {
    $course = new Course();
    expect($course->translatable)->toContain('alert');
});

it('can be created with factory', function () {
    $course = Course::factory()->create();
    expect($course)->toBeInstanceOf(Course::class)
        ->and($course->exists)->toBeTrue();
});

it('returns null for thumbnailImage when no images exist', function () {
    $course = Course::factory()->create();
    expect($course->thumbnailImage())->toBeNull();
});
