<?php

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Language;
use Testa\Models\EventDeliveryMethod;
use Testa\Models\News\Event;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(fn () => Language::factory()->create());

it('has eventType relationship', function () {
    $event = new Event();
    expect($event->eventType())->toBeInstanceOf(BelongsTo::class);
});

it('has venue relationship', function () {
    $event = new Event();
    expect($event->venue())->toBeInstanceOf(BelongsTo::class);
});

it('has speakers relationship', function () {
    $event = new Event();
    expect($event->speakers())->toBeInstanceOf(BelongsToMany::class);
});

it('has products relationship', function () {
    $event = new Event();
    expect($event->products())->toBeInstanceOf(BelongsToMany::class);
});

it('has attachments relationship', function () {
    $event = new Event();
    expect($event->attachments())->toBeInstanceOf(MorphMany::class);
});

it('casts starts_at to datetime', function () {
    $event = Event::factory()->create();
    expect($event->starts_at)->toBeInstanceOf(Carbon::class);
});

it('casts delivery_method to EventDeliveryMethod enum', function () {
    $event = Event::factory()->create();
    expect($event->delivery_method)->toBeInstanceOf(EventDeliveryMethod::class);
});

it('has translatable name field', function () {
    $event = new Event();
    expect($event->translatable)->toContain('name');
});

it('has translatable subtitle field', function () {
    $event = new Event();
    expect($event->translatable)->toContain('subtitle');
});

it('has translatable description field', function () {
    $event = new Event();
    expect($event->translatable)->toContain('description');
});

it('has translatable alert field', function () {
    $event = new Event();
    expect($event->translatable)->toContain('alert');
});

it('can be created with factory', function () {
    $event = Event::factory()->create();
    expect($event)->toBeInstanceOf(Event::class)
        ->and($event->exists)->toBeTrue();
});

it('can create in-person event with factory', function () {
    $event = Event::factory()->inPerson()->create();
    expect($event->delivery_method)->toBe(EventDeliveryMethod::IN_PERSON);
});

it('can create online event with factory', function () {
    $event = Event::factory()->online()->create();
    expect($event->delivery_method)->toBe(EventDeliveryMethod::ONLINE);
});

it('can create hybrid event with factory', function () {
    $event = Event::factory()->hybrid()->create();
    expect($event->delivery_method)->toBe(EventDeliveryMethod::HYBRID);
});
