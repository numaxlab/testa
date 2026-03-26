<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Channel;
use Lunar\Models\Collection as LunarCollection;
use Lunar\Models\CollectionGroup;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Models\Content\Tier;
use Testa\Storefront\Queries\Content\GetTierItineraries;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create(['default' => true]);
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);
    Channel::factory()->create(['default' => true]);
    CustomerGroup::factory()->create(['default' => true]);

    $this->group = CollectionGroup::factory()->create();
    $this->tier = Tier::factory()->create();
});

it('returns collections attached to the tier', function () {
    $collection = LunarCollection::factory()->create(['collection_group_id' => $this->group->id]);
    $this->tier->collections()->attach($collection->id);

    $result = new GetTierItineraries()->execute($this->tier->fresh(['collections']));

    expect($result)
        ->toHaveCount(1)
        ->and($result->first()->id)->toBe($collection->id);
});

it('does not return collections not attached to the tier', function () {
    LunarCollection::factory()->create(['collection_group_id' => $this->group->id]);

    $result = new GetTierItineraries()->execute($this->tier->fresh(['collections']));

    expect($result)->toBeEmpty();
});

it('returns collections ordered by _lft ascending', function () {
    $collections = LunarCollection::factory()->count(3)->create(['collection_group_id' => $this->group->id]);
    $collections->each(fn($c) => $this->tier->collections()->attach($c->id));

    $result = new GetTierItineraries()->execute($this->tier->fresh(['collections']));

    $lftValues = $result->pluck('_lft')->all();
    expect($lftValues)->toBe(collect($lftValues)->sort()->values()->all());
});

it('returns an empty collection when tier has no collections', function () {
    $result = new GetTierItineraries()->execute($this->tier->fresh(['collections']));

    expect($result)->toBeInstanceOf(Collection::class)->toBeEmpty();
});
