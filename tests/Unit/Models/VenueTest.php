<?php

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Language;
use Testa\Models\Venue;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(fn () => Language::factory()->create());

it('has events relationship', function () {
    $venue = new Venue();
    expect($venue->events())->toBeInstanceOf(HasMany::class);
});

it('has courseModules relationship', function () {
    $venue = new Venue();
    expect($venue->courseModules())->toBeInstanceOf(HasMany::class);
});

it('has translatable name field', function () {
    $venue = new Venue();
    expect($venue->translatable)->toContain('name');
});

it('can be created with factory', function () {
    $venue = Venue::factory()->create();
    expect($venue)->toBeInstanceOf(Venue::class)
        ->and($venue->exists)->toBeTrue();
});
