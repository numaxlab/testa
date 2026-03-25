<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Country;
use Lunar\Models\State;
use Testa\Storefront\Queries\GetStatesByCountry;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('returns states for the given country', function () {
    $country = Country::factory()->create();
    State::factory()->count(3)->create(['country_id' => $country->id]);

    $result = (new GetStatesByCountry())->execute($country->id);

    expect($result)->toHaveCount(3);
});

it('returns states ordered by name', function () {
    $country = Country::factory()->create();
    State::factory()->create(['country_id' => $country->id, 'name' => 'Zamora']);
    State::factory()->create(['country_id' => $country->id, 'name' => 'Aragon']);
    State::factory()->create(['country_id' => $country->id, 'name' => 'Madrid']);

    $result = (new GetStatesByCountry())->execute($country->id);

    expect($result->pluck('name')->all())->toBe(['Aragon', 'Madrid', 'Zamora']);
});

it('does not return states from other countries', function () {
    $country = Country::factory()->create();
    $otherCountry = Country::factory()->create();
    State::factory()->create(['country_id' => $otherCountry->id]);

    $result = (new GetStatesByCountry())->execute($country->id);

    expect($result)->toBeEmpty();
});

it('returns an empty collection when the country has no states', function () {
    $country = Country::factory()->create();

    $result = (new GetStatesByCountry())->execute($country->id);

    expect($result)->toBeEmpty();
});
