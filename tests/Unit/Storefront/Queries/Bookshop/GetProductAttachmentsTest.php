<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;
use Testa\Models\Attachment;
use Testa\Models\Education\CourseModule;
use Testa\Models\Media\Audio;
use Testa\Models\News\Event;
use Testa\Observers\CourseObserver;
use Testa\Storefront\Queries\Bookshop\GetProductAttachments;
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

    $productType = ProductType::factory()->create();
    $this->product = Product::factory()->create(['product_type_id' => $productType->id]);
});

function makePublishedAudio(): Audio
{
    return Audio::factory()->create(['is_published' => true]);
}

it('returns attachments from published events linked to the product', function () {
    $event = Event::factory()->create(['is_published' => true]);
    $event->products()->attach($this->product->id, ['position' => 1]);
    $audio = makePublishedAudio();
    Attachment::factory()->create([
        'attachable_type' => (new Event)->getMorphClass(),
        'attachable_id' => $event->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetProductAttachments()->execute($this->product);

    expect($result)->toHaveCount(1);
});

it('returns attachments from published course modules linked to the product', function () {
    $module = CourseModule::factory()->create(['is_published' => true]);
    $module->products()->attach($this->product->id, ['position' => 1]);
    $audio = makePublishedAudio();
    Attachment::factory()->create([
        'attachable_type' => (new CourseModule)->getMorphClass(),
        'attachable_id' => $module->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetProductAttachments()->execute($this->product);

    expect($result)->toHaveCount(1);
});

it('excludes attachments from unpublished events', function () {
    $event = Event::factory()->create(['is_published' => false]);
    $event->products()->attach($this->product->id, ['position' => 1]);
    $audio = makePublishedAudio();
    Attachment::factory()->create([
        'attachable_type' => (new Event)->getMorphClass(),
        'attachable_id' => $event->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetProductAttachments()->execute($this->product);

    expect($result)->toBeEmpty();
});

it('excludes attachments from unpublished course modules', function () {
    $module = CourseModule::factory()->create(['is_published' => false]);
    $module->products()->attach($this->product->id, ['position' => 1]);
    $audio = makePublishedAudio();
    Attachment::factory()->create([
        'attachable_type' => (new CourseModule)->getMorphClass(),
        'attachable_id' => $module->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetProductAttachments()->execute($this->product);

    expect($result)->toBeEmpty();
});

it('excludes attachments with unpublished media', function () {
    $event = Event::factory()->create(['is_published' => true]);
    $event->products()->attach($this->product->id, ['position' => 1]);
    $audio = Audio::factory()->create(['is_published' => false]);
    Attachment::factory()->create([
        'attachable_type' => (new Event)->getMorphClass(),
        'attachable_id' => $event->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetProductAttachments()->execute($this->product);

    expect($result)->toBeEmpty();
});

it('does not return attachments from events linked to other products', function () {
    $otherProduct = Product::factory()->create(['product_type_id' => $this->product->product_type_id]);
    $event = Event::factory()->create(['is_published' => true]);
    $event->products()->attach($otherProduct->id, ['position' => 1]);
    $audio = makePublishedAudio();
    Attachment::factory()->create([
        'attachable_type' => (new Event)->getMorphClass(),
        'attachable_id' => $event->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetProductAttachments()->execute($this->product);

    expect($result)->toBeEmpty();
});

it('eager loads media', function () {
    $event = Event::factory()->create(['is_published' => true]);
    $event->products()->attach($this->product->id, ['position' => 1]);
    $audio = makePublishedAudio();
    Attachment::factory()->create([
        'attachable_type' => (new Event)->getMorphClass(),
        'attachable_id' => $event->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetProductAttachments()->execute($this->product);

    expect($result->first()->relationLoaded('media'))->toBeTrue();
});

it('returns an empty collection when product has no activities', function () {
    $result = new GetProductAttachments()->execute($this->product);

    expect($result)->toBeEmpty();
});
