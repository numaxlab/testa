<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\TaxClass;
use Testa\Models\Attachment;
use Testa\Models\Education\Course;
use Testa\Models\Education\CourseModule;
use Testa\Models\Media\Audio;
use Testa\Observers\CourseObserver;
use Testa\Storefront\Queries\Education\GetCourseAttachments;
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

it('returns attachments directly on the course', function () {
    $audio = Audio::factory()->create(['is_published' => true]);
    Attachment::factory()->create([
        'attachable_type' => (new Course)->getMorphClass(),
        'attachable_id' => $this->course->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetCourseAttachments()->execute($this->course);

    expect($result)->toHaveCount(1);
});

it('returns attachments on course modules', function () {
    $module = CourseModule::factory()->create(['course_id' => $this->course->id, 'is_published' => true]);
    $audio = Audio::factory()->create(['is_published' => true]);
    Attachment::factory()->create([
        'attachable_type' => (new CourseModule)->getMorphClass(),
        'attachable_id' => $module->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetCourseAttachments()->execute($this->course->fresh());

    expect($result)->toHaveCount(1);
});

it('excludes attachments with unpublished media', function () {
    $audio = Audio::factory()->create(['is_published' => false]);
    Attachment::factory()->create([
        'attachable_type' => (new Course)->getMorphClass(),
        'attachable_id' => $this->course->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetCourseAttachments()->execute($this->course);

    expect($result)->toBeEmpty();
});

it('does not return attachments from other courses', function () {
    $otherCourse = Course::factory()->create();
    $audio = Audio::factory()->create(['is_published' => true]);
    Attachment::factory()->create([
        'attachable_type' => (new Course)->getMorphClass(),
        'attachable_id' => $otherCourse->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetCourseAttachments()->execute($this->course);

    expect($result)->toBeEmpty();
});

it('does not return attachments from modules of other courses', function () {
    $otherCourse = Course::factory()->create();
    $module = CourseModule::factory()->create(['course_id' => $otherCourse->id]);
    $audio = Audio::factory()->create(['is_published' => true]);
    Attachment::factory()->create([
        'attachable_type' => (new CourseModule)->getMorphClass(),
        'attachable_id' => $module->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetCourseAttachments()->execute($this->course->fresh());

    expect($result)->toBeEmpty();
});

it('eager loads media', function () {
    $audio = Audio::factory()->create(['is_published' => true]);
    Attachment::factory()->create([
        'attachable_type' => (new Course)->getMorphClass(),
        'attachable_id' => $this->course->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetCourseAttachments()->execute($this->course);

    expect($result->first()->relationLoaded('media'))->toBeTrue();
});

it('returns an empty collection when course has no attachments', function () {
    $result = new GetCourseAttachments()->execute($this->course);

    expect($result)->toBeInstanceOf(Collection::class)->toBeEmpty();
});
