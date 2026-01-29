<?php

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Language;
use Testa\Models\Attachment;
use Testa\Models\Media\Audio;
use Testa\Models\Media\Document;
use Testa\Models\Media\Video;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(fn () => Language::factory()->create());

it('has attachable relationship', function () {
    $attachment = new Attachment();
    expect($attachment->attachable())->toBeInstanceOf(MorphTo::class);
});

it('has media relationship', function () {
    $attachment = new Attachment();
    expect($attachment->media())->toBeInstanceOf(MorphTo::class);
});

it('returns name from media', function () {
    $audio = Audio::factory()->create(['name' => 'Test Audio Name']);
    $attachment = Attachment::factory()->forAudio()->create([
        'media_id' => $audio->id,
    ]);

    expect($attachment->name)->toBe('Test Audio Name');
});

it('returns component_namespace for videos', function () {
    $video = Video::factory()->create();
    $attachment = new Attachment([
        'media_type' => (new Video)->getMorphClass(),
        'media_id' => $video->id,
    ]);

    expect($attachment->component_namespace)->toBe('videos');
});

it('returns component_namespace for audios', function () {
    $audio = Audio::factory()->create();
    $attachment = new Attachment([
        'media_type' => (new Audio)->getMorphClass(),
        'media_id' => $audio->id,
    ]);

    expect($attachment->component_namespace)->toBe('audios');
});

it('returns component_namespace for documents', function () {
    $document = Document::factory()->create();
    $attachment = new Attachment([
        'media_type' => (new Document)->getMorphClass(),
        'media_id' => $document->id,
    ]);

    expect($attachment->component_namespace)->toBe('documents');
});

it('throws exception for unsupported media type', function () {
    $attachment = new Attachment([
        'media_type' => 'unsupported_type',
        'media_id' => 1,
    ]);

    $attachment->component_namespace;
})->throws(\RuntimeException::class, 'Unsupported media type');

it('can be created with factory', function () {
    $attachment = Attachment::factory()->create();
    expect($attachment)->toBeInstanceOf(Attachment::class)
        ->and($attachment->exists)->toBeTrue();
});
