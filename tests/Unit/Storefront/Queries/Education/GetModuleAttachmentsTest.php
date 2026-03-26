<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\TaxClass;
use Testa\Models\Attachment;
use Testa\Models\Education\CourseModule;
use Testa\Models\Media\Audio;
use Testa\Observers\CourseObserver;
use Testa\Storefront\Queries\Education\GetModuleAttachments;
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

    $this->module = CourseModule::factory()->create(['is_published' => true]);
});

it('returns attachments for the module with published media', function () {
    $audio = Audio::factory()->create(['is_published' => true]);
    Attachment::factory()->create([
        'attachable_type' => (new CourseModule)->getMorphClass(),
        'attachable_id' => $this->module->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetModuleAttachments()->execute($this->module);

    expect($result)->toHaveCount(1);
});

it('excludes attachments with unpublished media', function () {
    $audio = Audio::factory()->create(['is_published' => false]);
    Attachment::factory()->create([
        'attachable_type' => (new CourseModule)->getMorphClass(),
        'attachable_id' => $this->module->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetModuleAttachments()->execute($this->module);

    expect($result)->toBeEmpty();
});

it('does not return attachments from other modules', function () {
    $otherModule = CourseModule::factory()->create(['is_published' => true]);
    $audio = Audio::factory()->create(['is_published' => true]);
    Attachment::factory()->create([
        'attachable_type' => (new CourseModule)->getMorphClass(),
        'attachable_id' => $otherModule->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetModuleAttachments()->execute($this->module);

    expect($result)->toBeEmpty();
});

it('eager loads media', function () {
    $audio = Audio::factory()->create(['is_published' => true]);
    Attachment::factory()->create([
        'attachable_type' => (new CourseModule)->getMorphClass(),
        'attachable_id' => $this->module->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetModuleAttachments()->execute($this->module);

    expect($result->first()->relationLoaded('media'))->toBeTrue();
});

it('returns an empty collection when module has no attachments', function () {
    $result = new GetModuleAttachments()->execute($this->module);

    expect($result)->toBeInstanceOf(Collection::class)->toBeEmpty();
});
