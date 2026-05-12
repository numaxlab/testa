<?php

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\TaxClass;
use Testa\Models\Education\CourseModule;
use Testa\Models\News\Event;
use Testa\Models\News\EventType;
use Testa\Observers\CourseObserver;
use Testa\Storefront\Queries\News\GetActivities;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);
    Channel::factory()->create(['default' => true]);
    CustomerGroup::factory()->create(['default' => true]);

    $productOption = ProductOption::factory()->create([
        'handle' => CourseObserver::RATE_PRODUCT_OPTION_HANDLE,
    ]);
    ProductOptionValue::factory()->create(['product_option_id' => $productOption->id]);
});

it('returns a paginator', function () {
    $result = new GetActivities()->execute();

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
});

it('excludes past events when no search is applied', function () {
    Event::factory()->create(['is_published' => true, 'starts_at' => now()->subDay()]);

    $result = new GetActivities()->execute();

    expect($result->total())->toBe(0);
});

it('excludes past course modules when no search is applied', function () {
    CourseModule::factory()->create(['is_published' => true, 'starts_at' => now()->subDay()]);

    $result = new GetActivities()->execute();

    expect($result->total())->toBe(0);
});

it('excludes unpublished events when no search is applied', function () {
    Event::factory()->create(['is_published' => false, 'starts_at' => now()->addDay()]);

    $result = new GetActivities()->execute();

    expect($result->total())->toBe(0);
});

it('excludes unpublished course modules when no search is applied', function () {
    CourseModule::factory()->create(['is_published' => false, 'starts_at' => now()->addDay()]);

    $result = new GetActivities()->execute();

    expect($result->total())->toBe(0);
});

it('includes future events and course modules when no search is applied', function () {
    $event = Event::factory()->create(['is_published' => true, 'starts_at' => now()->addDays(1)]);
    $module = CourseModule::factory()->create(['is_published' => true, 'starts_at' => now()->addDays(2)]);

    $result = new GetActivities()->execute();
    $ids = $result->getCollection()->pluck('id');

    expect($ids)->toContain($event->id)->toContain($module->id);
});

it('orders future activities ascending when no search is applied', function () {
    $later = Event::factory()->create(['is_published' => true, 'starts_at' => now()->addDays(5)]);
    $sooner = Event::factory()->create(['is_published' => true, 'starts_at' => now()->addDays(2)]);

    $result = new GetActivities()->execute();
    $ids = $result->getCollection()->pluck('id')->values();

    expect($ids->all())->toBe([$sooner->id, $later->id]);
});

it('returns only course modules when type is c', function () {
    $module = CourseModule::factory()->create(['is_published' => true, 'starts_at' => now()->addDay()]);
    Event::factory()->create(['is_published' => true, 'starts_at' => now()->addDay()]);

    $result = new GetActivities()->execute(type: 'c');

    expect($result->total())->toBe(1)
        ->and($result->getCollection()->first()->id)->toBe($module->id);
});

it('includes past course modules when type is c', function () {
    CourseModule::factory()->create(['is_published' => true, 'starts_at' => now()->subDay()]);

    $result = new GetActivities()->execute(type: 'c');

    expect($result->total())->toBe(1);
});

it('excludes unpublished course modules when type is c', function () {
    CourseModule::factory()->create(['is_published' => false, 'starts_at' => now()->subDay()]);

    $result = new GetActivities()->execute(type: 'c');

    expect($result->total())->toBe(0);
});

it('orders course modules future-asc then past-desc when type is c', function () {
    $pastOlder = CourseModule::factory()->create(['is_published' => true, 'starts_at' => now()->subDays(5)]);
    $pastRecent = CourseModule::factory()->create(['is_published' => true, 'starts_at' => now()->subDay()]);
    $futureFar = CourseModule::factory()->create(['is_published' => true, 'starts_at' => now()->addDays(5)]);
    $futureNear = CourseModule::factory()->create(['is_published' => true, 'starts_at' => now()->addDay()]);

    $result = new GetActivities()->execute(type: 'c');
    $ids = $result->getCollection()->pluck('id')->values();

    expect($ids->all())->toBe([$futureNear->id, $futureFar->id, $pastRecent->id, $pastOlder->id]);
});

it('returns only events of the given type', function () {
    $type = EventType::factory()->create();
    $otherType = EventType::factory()->create();

    $matching = Event::factory()->create(['is_published' => true, 'event_type_id' => $type->id, 'starts_at' => now()->addDay()]);
    Event::factory()->create(['is_published' => true, 'event_type_id' => $otherType->id, 'starts_at' => now()->addDay()]);

    $result = new GetActivities()->execute(type: (string)$type->id);

    expect($result->total())->toBe(1)
        ->and($result->getCollection()->first()->id)->toBe($matching->id);
});

it('includes past events when filtering by event type', function () {
    $type = EventType::factory()->create();
    Event::factory()->create(['is_published' => true, 'event_type_id' => $type->id, 'starts_at' => now()->subDay()]);

    $result = new GetActivities()->execute(type: (string)$type->id);

    expect($result->total())->toBe(1);
});

it('excludes unpublished events when filtering by event type', function () {
    $type = EventType::factory()->create();
    Event::factory()->create(['is_published' => false, 'event_type_id' => $type->id, 'starts_at' => now()->subDay()]);

    $result = new GetActivities()->execute(type: (string)$type->id);

    expect($result->total())->toBe(0);
});

it('orders events future-asc then past-desc when filtering by event type', function () {
    $type = EventType::factory()->create();

    $pastOlder = Event::factory()->create(['is_published' => true, 'event_type_id' => $type->id, 'starts_at' => now()->subDays(5)]);
    $pastRecent = Event::factory()->create(['is_published' => true, 'event_type_id' => $type->id, 'starts_at' => now()->subDay()]);
    $futureFar = Event::factory()->create(['is_published' => true, 'event_type_id' => $type->id, 'starts_at' => now()->addDays(5)]);
    $futureNear = Event::factory()->create(['is_published' => true, 'event_type_id' => $type->id, 'starts_at' => now()->addDay()]);

    $result = new GetActivities()->execute(type: (string)$type->id);
    $ids = $result->getCollection()->pluck('id')->values();

    expect($ids->all())->toBe([$futureNear->id, $futureFar->id, $pastRecent->id, $pastOlder->id]);
});
