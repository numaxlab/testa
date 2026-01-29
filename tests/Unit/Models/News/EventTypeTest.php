<?php

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Language;
use Testa\Models\News\EventType;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(fn () => Language::factory()->create());

it('has events relationship', function () {
    $eventType = new EventType();
    expect($eventType->events())->toBeInstanceOf(HasMany::class);
});

it('has translatable name field', function () {
    $eventType = new EventType();
    expect($eventType->translatable)->toContain('name');
});

it('can be created with factory', function () {
    $eventType = EventType::factory()->create();
    expect($eventType)->toBeInstanceOf(EventType::class)
        ->and($eventType->exists)->toBeTrue();
});
