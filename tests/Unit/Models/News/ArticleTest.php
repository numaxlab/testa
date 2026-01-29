<?php

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Language;
use Testa\Models\News\Article;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(fn () => Language::factory()->create());

it('has products relationship', function () {
    $article = new Article();
    expect($article->products())->toBeInstanceOf(BelongsToMany::class);
});

it('casts published_at to datetime', function () {
    $article = Article::factory()->create();
    expect($article->published_at)->toBeInstanceOf(Carbon::class);
});

it('has translatable name field', function () {
    $article = new Article();
    expect($article->translatable)->toContain('name');
});

it('has translatable summary field', function () {
    $article = new Article();
    expect($article->translatable)->toContain('summary');
});

it('has translatable content field', function () {
    $article = new Article();
    expect($article->translatable)->toContain('content');
});

it('can be created with factory', function () {
    $article = Article::factory()->create();
    expect($article)->toBeInstanceOf(Article::class)
        ->and($article->exists)->toBeTrue();
});

it('can create unpublished article with factory', function () {
    $article = Article::factory()->unpublished()->create();
    expect($article->is_published)->toBeFalse();
});
