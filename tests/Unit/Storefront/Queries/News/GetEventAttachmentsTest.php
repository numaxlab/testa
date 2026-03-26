<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Models\Attachment;
use Testa\Models\Media\Audio;
use Testa\Models\Media\Visibility;
use Testa\Models\News\Event;
use Testa\Storefront\Queries\News\GetEventAttachments;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create(['default' => true]);
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);

    $this->event = Event::factory()->create(['is_published' => true]);
});

it('returns attachments for the event with published public media', function () {
    $audio = Audio::factory()->create(['is_published' => true, 'visibility' => Visibility::PUBLIC->value]);
    Attachment::factory()->create([
        'attachable_type' => (new Event)->getMorphClass(),
        'attachable_id' => $this->event->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetEventAttachments()->execute($this->event);

    expect($result)->toHaveCount(1);
});

it('excludes attachments with unpublished media', function () {
    $audio = Audio::factory()->create(['is_published' => false, 'visibility' => Visibility::PUBLIC->value]);
    Attachment::factory()->create([
        'attachable_type' => (new Event)->getMorphClass(),
        'attachable_id' => $this->event->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetEventAttachments()->execute($this->event);

    expect($result)->toBeEmpty();
});

it('excludes attachments with private media', function () {
    $audio = Audio::factory()->create(['is_published' => true, 'visibility' => Visibility::PRIVATE->value]);
    Attachment::factory()->create([
        'attachable_type' => (new Event)->getMorphClass(),
        'attachable_id' => $this->event->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetEventAttachments()->execute($this->event);

    expect($result)->toBeEmpty();
});

it('does not return attachments from other events', function () {
    $otherEvent = Event::factory()->create(['is_published' => true]);
    $audio = Audio::factory()->create(['is_published' => true, 'visibility' => Visibility::PUBLIC->value]);
    Attachment::factory()->create([
        'attachable_type' => (new Event)->getMorphClass(),
        'attachable_id' => $otherEvent->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetEventAttachments()->execute($this->event);

    expect($result)->toBeEmpty();
});

it('eager loads media', function () {
    $audio = Audio::factory()->create(['is_published' => true, 'visibility' => Visibility::PUBLIC->value]);
    Attachment::factory()->create([
        'attachable_type' => (new Event)->getMorphClass(),
        'attachable_id' => $this->event->id,
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    $result = new GetEventAttachments()->execute($this->event);

    expect($result->first()->relationLoaded('media'))->toBeTrue();
});

it('returns an empty collection when event has no attachments', function () {
    $result = new GetEventAttachments()->execute($this->event);

    expect($result)->toBeInstanceOf(Collection::class)->toBeEmpty();
});
