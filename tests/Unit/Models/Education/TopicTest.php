<?php

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Language;
use Testa\Models\Education\Topic;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(fn () => Language::factory()->create());

it('has courses relationship', function () {
    $topic = new Topic();
    expect($topic->courses())->toBeInstanceOf(HasMany::class);
});

it('has translatable name field', function () {
    $topic = new Topic();
    expect($topic->translatable)->toContain('name');
});

it('has translatable subtitle field', function () {
    $topic = new Topic();
    expect($topic->translatable)->toContain('subtitle');
});

it('has translatable description field', function () {
    $topic = new Topic();
    expect($topic->translatable)->toContain('description');
});

it('uses education_topics table', function () {
    $topic = new Topic();
    expect($topic->getTable())->toBe('education_topics');
});

it('can be created with factory', function () {
    $topic = Topic::factory()->create();
    expect($topic)->toBeInstanceOf(Topic::class)
        ->and($topic->exists)->toBeTrue();
});
