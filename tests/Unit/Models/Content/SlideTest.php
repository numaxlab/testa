<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Language;
use Testa\Models\Content\Section;
use Testa\Models\Content\Slide;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(fn () => Language::factory()->create());

it('casts section to Section enum', function () {
    $slide = Slide::factory()->create();
    expect($slide->section)->toBeInstanceOf(Section::class);
});

it('has translatable name field', function () {
    $slide = new Slide();
    expect($slide->translatable)->toContain('name');
});

it('has translatable description field', function () {
    $slide = new Slide();
    expect($slide->translatable)->toContain('description');
});

it('has translatable link field', function () {
    $slide = new Slide();
    expect($slide->translatable)->toContain('link');
});

it('has translatable button_text field', function () {
    $slide = new Slide();
    expect($slide->translatable)->toContain('button_text');
});

it('can be created with factory', function () {
    $slide = Slide::factory()->create();
    expect($slide)->toBeInstanceOf(Slide::class)
        ->and($slide->exists)->toBeTrue();
});

it('can create homepage slide with factory', function () {
    $slide = Slide::factory()->homepage()->create();
    expect($slide->section)->toBe(Section::HOMEPAGE);
});

it('can create bookshop slide with factory', function () {
    $slide = Slide::factory()->bookshop()->create();
    expect($slide->section)->toBe(Section::BOOKSHOP);
});
