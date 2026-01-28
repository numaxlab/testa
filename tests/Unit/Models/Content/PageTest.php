<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Language;
use Testa\Models\Content\Page;
use Testa\Models\Content\Section;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(fn() => Language::factory()->create());

it('casts section to Section enum', function () {
    $page = new Page(['section' => Section::BOOKSHOP]);
    expect($page->section)->toBeInstanceOf(Section::class);
});

it('casts content to array', function () {
    $page = Page::factory()->create();
    expect($page->content)->toBeArray();
});

it('returns correct has_breadcrumb attribute', function (?Section $section, bool $expected) {
    $page = new Page();
    $page->section = $section;
    expect($page->has_breadcrumb)->toBe($expected);
})->with([
    [Section::BOOKSHOP, true],
    [Section::EDITORIAL, true],
    [Section::EDUCATION, true],
    [Section::HOMEPAGE, false],
    [null, false],
]);

it('returns correct breadcrumb_route_name attribute', function (?Section $section, ?string $expected) {
    $page = new Page();
    $page->section = $section;
    expect($page->breadcrumb_route_name)->toBe($expected);
})->with([
    [Section::BOOKSHOP, 'testa.storefront.bookshop.homepage'],
    [Section::EDITORIAL, 'testa.storefront.editorial.homepage'],
    [Section::EDUCATION, 'testa.storefront.education.homepage'],
    [Section::HOMEPAGE, null],
    [null, null],
]);

it('returns correct human_section attribute', function (?Section $section, ?string $expected) {
    $page = new Page();
    $page->section = $section;
    expect($page->human_section)->toBe($expected);
})->with([
    [Section::BOOKSHOP, 'Librería'],
    [Section::EDITORIAL, 'Editorial'],
    [Section::EDUCATION, 'Formación'],
    [Section::HOMEPAGE, null],
    [null, null],
]);

