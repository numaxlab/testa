<?php

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Language;
use Testa\Models\Content\Section;
use Testa\Models\Content\Tier;
use Testa\Models\Content\TierType;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(fn() => Language::factory()->create());

it('casts section to Section enum', function () {
    $tier = Tier::factory()->create();
    expect($tier->section)->toBeInstanceOf(Section::class);
});

it('casts type to TierType enum', function () {
    $tier = Tier::factory()->create();
    expect($tier->type)->toBeInstanceOf(TierType::class);
});

it('has banners relationship', function () {
    $tier = new Tier();
    expect($tier->banners())->toBeInstanceOf(BelongsToMany::class);
});

it('has collections relationship', function () {
    $tier = new Tier();
    expect($tier->collections())->toBeInstanceOf(BelongsToMany::class);
});

it('has courses relationship', function () {
    $tier = new Tier();
    expect($tier->courses())->toBeInstanceOf(BelongsToMany::class);
});

it('has educationTopics relationship', function () {
    $tier = new Tier();
    expect($tier->educationTopics())->toBeInstanceOf(BelongsToMany::class);
});

it('has attachments relationship', function () {
    $tier = new Tier();
    expect($tier->attachments())->toBeInstanceOf(MorphMany::class);
});

it('tells it has link or not', function () {
    $tier = Tier::factory()->create([
        'link' => null,
    ]);

    expect($tier->has_link)->toBeFalse();

    $tier->link = 'https://example.com';
    $tier->link_name = 'Example';
    expect($tier->has_link)->toBeTrue();
});

it('returns livewire component attribute', function (?TierType $tierType, ?string $expected) {
    $tier = new Tier(['type' => $tierType?->value]);

    expect($tier->livewire_component)->toBe($expected);
})->with([
    [TierType::RELATED_CONTENT_BANNER, 'banner'],
    [TierType::RELATED_CONTENT_COLLECTION, 'collection'],
    [TierType::RELATED_CONTENT_COURSE, 'courses'],
    [TierType::RELATED_CONTENT_EDUCATION_TOPIC, 'education-topics'],
    [TierType::RELATED_CONTENT_MEDIA, 'media'],
    [TierType::EDITORIAL_LATEST, 'editorial-latest'],
    [TierType::EDUCATION_UPCOMING, 'education-upcoming'],
    [TierType::EVENTS_UPCOMING, 'events-upcoming'],
    [TierType::ARTICLES_LATEST, 'articles-latest'],
    [null, null],
]);