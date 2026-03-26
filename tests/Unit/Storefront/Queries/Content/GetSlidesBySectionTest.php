<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Testa\Models\Content\Section;
use Testa\Models\Content\Slide;
use Testa\Storefront\Queries\Content\GetSlidesBySection;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('returns only published slides for the given section', function () {
    Slide::factory()->create(['section' => Section::BOOKSHOP->value, 'is_published' => true]);

    $result = new GetSlidesBySection()->execute(Section::BOOKSHOP);

    expect($result)->toHaveCount(1);
});

it('excludes unpublished slides', function () {
    Slide::factory()->create(['section' => Section::BOOKSHOP->value, 'is_published' => false]);

    $result = new GetSlidesBySection()->execute(Section::BOOKSHOP);

    expect($result)->toBeEmpty();
});

it('excludes slides from other sections', function () {
    Slide::factory()->create(['section' => Section::EDITORIAL->value, 'is_published' => true]);

    $result = new GetSlidesBySection()->execute(Section::BOOKSHOP);

    expect($result)->toBeEmpty();
});

it('returns slides ordered by sort_position', function () {
    Slide::factory()->create(['section' => Section::BOOKSHOP->value, 'is_published' => true, 'sort_position' => 3]);
    Slide::factory()->create(['section' => Section::BOOKSHOP->value, 'is_published' => true, 'sort_position' => 1]);
    Slide::factory()->create(['section' => Section::BOOKSHOP->value, 'is_published' => true, 'sort_position' => 2]);

    $result = new GetSlidesBySection()->execute(Section::BOOKSHOP);

    expect($result->pluck('sort_position')->map(fn($v) => (int) $v)->all())->toBe([1, 2, 3]);
});

it('eager loads media', function () {
    Slide::factory()->create(['section' => Section::BOOKSHOP->value, 'is_published' => true]);

    $result = new GetSlidesBySection()->execute(Section::BOOKSHOP);

    expect($result->first()->relationLoaded('media'))->toBeTrue();
});
