<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\TaxClass;
use NumaxLab\Lunar\Geslib\Models\Author;
use Testa\Models\Attachment;
use Testa\Models\Education\CourseModule;
use Testa\Models\Media\Audio;
use Testa\Models\Media\Visibility;
use Testa\Observers\CourseObserver;
use Testa\Storefront\Queries\Education\GetPublicAttachmentsForModules;
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

    $author = Author::factory()->create();
    $this->module = CourseModule::factory()->create(['is_published' => true]);
    $this->module->instructors()->attach($author->id, ['position' => 1]);
    $this->modules = Collection::make([$this->module]);
});

it('returns a collection', function () {
    $result = new GetPublicAttachmentsForModules()->execute($this->modules);

    expect($result)->toBeInstanceOf(Collection::class);
});

it('returns attachments for modules with public published media', function () {
    $audio = Audio::factory()->create(['is_published' => true, 'visibility' => Visibility::PUBLIC->value]);
    Attachment::factory()->create([
        'attachable_type' => (new CourseModule)->getMorphClass(),
        'attachable_id' => $this->module->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetPublicAttachmentsForModules()->execute($this->modules);

    expect($result)->toHaveCount(1);
});

it('excludes attachments with unpublished media', function () {
    $audio = Audio::factory()->create(['is_published' => false, 'visibility' => Visibility::PUBLIC->value]);
    Attachment::factory()->create([
        'attachable_type' => (new CourseModule)->getMorphClass(),
        'attachable_id' => $this->module->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetPublicAttachmentsForModules()->execute($this->modules);

    expect($result)->toBeEmpty();
});

it('excludes attachments with private media', function () {
    $audio = Audio::factory()->create(['is_published' => true, 'visibility' => Visibility::PRIVATE->value]);
    Attachment::factory()->create([
        'attachable_type' => (new CourseModule)->getMorphClass(),
        'attachable_id' => $this->module->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetPublicAttachmentsForModules()->execute($this->modules);

    expect($result)->toBeEmpty();
});

it('excludes attachments not belonging to the given modules', function () {
    $otherModule = CourseModule::factory()->create(['is_published' => true]);
    $audio = Audio::factory()->create(['is_published' => true, 'visibility' => Visibility::PUBLIC->value]);
    Attachment::factory()->create([
        'attachable_type' => (new CourseModule)->getMorphClass(),
        'attachable_id' => $otherModule->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetPublicAttachmentsForModules()->execute($this->modules);

    expect($result)->toBeEmpty();
});

it('eager loads media', function () {
    $audio = Audio::factory()->create(['is_published' => true, 'visibility' => Visibility::PUBLIC->value]);
    Attachment::factory()->create([
        'attachable_type' => (new CourseModule)->getMorphClass(),
        'attachable_id' => $this->module->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetPublicAttachmentsForModules()->execute($this->modules);

    expect($result->first()->relationLoaded('media'))->toBeTrue();
});

it('returns an empty collection when given an empty modules collection', function () {
    $result = new GetPublicAttachmentsForModules()->execute(new Collection());

    expect($result)->toBeEmpty();
});
