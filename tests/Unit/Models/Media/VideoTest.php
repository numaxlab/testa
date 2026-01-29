<?php

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Language;
use Testa\Models\Media\Video;
use Testa\Models\Media\Visibility;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(fn () => Language::factory()->create());

it('implements Media interface', function () {
    $video = new Video();
    expect($video)->toBeInstanceOf(\Testa\Models\Media\Media::class);
});

it('has attachments relationship', function () {
    $video = new Video();
    expect($video->attachments())->toBeInstanceOf(MorphMany::class);
});

it('casts visibility to Visibility enum', function () {
    $video = Video::factory()->create();
    expect($video->visibility)->toBeInstanceOf(Visibility::class);
});

it('has translatable name field', function () {
    $video = new Video();
    expect($video->translatable)->toContain('name');
});

it('has translatable description field', function () {
    $video = new Video();
    expect($video->translatable)->toContain('description');
});

it('can be created with factory', function () {
    $video = Video::factory()->create();
    expect($video)->toBeInstanceOf(Video::class)
        ->and($video->exists)->toBeTrue();
});

it('can create public video with factory', function () {
    $video = Video::factory()->public()->create();
    expect($video->visibility)->toBe(Visibility::PUBLIC);
});

it('can create private video with factory', function () {
    $video = Video::factory()->private()->create();
    expect($video->visibility)->toBe(Visibility::PRIVATE);
});
