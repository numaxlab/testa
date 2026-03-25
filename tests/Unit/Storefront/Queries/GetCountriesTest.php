<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Country;
use Testa\Storefront\Queries\GetCountries;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('returns all countries', function () {
    Country::factory()->count(3)->create();

    $result = (new GetCountries())->execute();

    expect($result)->toHaveCount(3);
});

it('returns countries ordered by native name', function () {
    Country::factory()->create(['native' => 'Zulu']);
    Country::factory()->create(['native' => 'Alemán']);
    Country::factory()->create(['native' => 'Magyar']);

    $result = (new GetCountries())->execute();

    expect($result->pluck('native')->all())->toBe(['Alemán', 'Magyar', 'Zulu']);
});

it('returns an empty collection when no countries exist', function () {
    $result = (new GetCountries())->execute();

    expect($result)->toBeEmpty();
});
