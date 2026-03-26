<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Models\Media\Document;
use Testa\Models\Media\Visibility;
use Testa\Storefront\Queries\Media\GetLatestPublicDocuments;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create(['default' => true]);
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);
});

it('returns published public documents', function () {
    Document::factory()->count(3)->create([
        'is_published' => true,
        'visibility' => Visibility::PUBLIC->value,
    ]);

    $result = new GetLatestPublicDocuments()->execute();

    expect($result)->toHaveCount(3);
});

it('excludes unpublished documents', function () {
    Document::factory()->create([
        'is_published' => false,
        'visibility' => Visibility::PUBLIC->value,
    ]);

    $result = new GetLatestPublicDocuments()->execute();

    expect($result)->toBeEmpty();
});

it('excludes private documents', function () {
    Document::factory()->create([
        'is_published' => true,
        'visibility' => Visibility::PRIVATE->value,
    ]);

    $result = new GetLatestPublicDocuments()->execute();

    expect($result)->toBeEmpty();
});

it('limits results to the given limit', function () {
    Document::factory()->count(10)->create([
        'is_published' => true,
        'visibility' => Visibility::PUBLIC->value,
    ]);

    $result = new GetLatestPublicDocuments()->execute(4);

    expect($result)->toHaveCount(4);
});

it('defaults to a limit of 6', function () {
    Document::factory()->count(10)->create([
        'is_published' => true,
        'visibility' => Visibility::PUBLIC->value,
    ]);

    $result = new GetLatestPublicDocuments()->execute();

    expect($result)->toHaveCount(6);
});

it('returns documents ordered by latest first', function () {
    Document::factory()->create([
        'is_published' => true,
        'visibility' => Visibility::PUBLIC->value,
        'created_at' => now()->subDays(2),
    ]);
    Document::factory()->create([
        'is_published' => true,
        'visibility' => Visibility::PUBLIC->value,
        'created_at' => now(),
    ]);

    $result = new GetLatestPublicDocuments()->execute();

    expect($result->first()->created_at->gt($result->last()->created_at))->toBeTrue();
});

it('returns an empty collection when no documents exist', function () {
    $result = new GetLatestPublicDocuments()->execute();

    expect($result)->toBeEmpty();
});
