<?php

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Language;
use Testa\Models\Media\Audio;
use Testa\Models\Media\Visibility;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(fn () => Language::factory()->create());

it('implements Media interface', function () {
    $audio = new Audio();
    expect($audio)->toBeInstanceOf(\Testa\Models\Media\Media::class);
});

it('has attachments relationship', function () {
    $audio = new Audio();
    expect($audio->attachments())->toBeInstanceOf(MorphMany::class);
});

it('casts visibility to Visibility enum', function () {
    $audio = Audio::factory()->create();
    expect($audio->visibility)->toBeInstanceOf(Visibility::class);
});

it('has translatable name field', function () {
    $audio = new Audio();
    expect($audio->translatable)->toContain('name');
});

it('has translatable description field', function () {
    $audio = new Audio();
    expect($audio->translatable)->toContain('description');
});

it('returns is_private true when visibility is private', function () {
    $audio = Audio::factory()->private()->create();
    expect($audio->is_private)->toBeTrue();
});

it('returns is_private false when visibility is public', function () {
    $audio = Audio::factory()->public()->create();
    expect($audio->is_private)->toBeFalse();
});

it('can be created with factory', function () {
    $audio = Audio::factory()->create();
    expect($audio)->toBeInstanceOf(Audio::class)
        ->and($audio->exists)->toBeTrue();
});
