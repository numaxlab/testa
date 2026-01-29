<?php

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Language;
use Testa\Models\Media\Document;
use Testa\Models\Media\Visibility;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(fn () => Language::factory()->create());

it('implements Media interface', function () {
    $document = new Document();
    expect($document)->toBeInstanceOf(\Testa\Models\Media\Media::class);
});

it('has attachments relationship', function () {
    $document = new Document();
    expect($document->attachments())->toBeInstanceOf(MorphMany::class);
});

it('casts visibility to Visibility enum', function () {
    $document = Document::factory()->create();
    expect($document->visibility)->toBeInstanceOf(Visibility::class);
});

it('has translatable name field', function () {
    $document = new Document();
    expect($document->translatable)->toContain('name');
});

it('has translatable description field', function () {
    $document = new Document();
    expect($document->translatable)->toContain('description');
});

it('can be created with factory', function () {
    $document = Document::factory()->create();
    expect($document)->toBeInstanceOf(Document::class)
        ->and($document->exists)->toBeTrue();
});

it('can create public document with factory', function () {
    $document = Document::factory()->public()->create();
    expect($document->visibility)->toBe(Visibility::PUBLIC);
});

it('can create private document with factory', function () {
    $document = Document::factory()->private()->create();
    expect($document->visibility)->toBe(Visibility::PRIVATE);
});
