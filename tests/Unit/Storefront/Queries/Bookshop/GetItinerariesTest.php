<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Channel;
use Lunar\Models\Collection as LunarCollection;
use Lunar\Models\CollectionGroup;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use NumaxLab\Lunar\Geslib\Handle;
use Testa\Storefront\Queries\Bookshop\GetItineraries;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);
    Channel::factory()->create(['default' => true]);
    CustomerGroup::factory()->create(['default' => true]);

    $this->itinerariesGroup = CollectionGroup::factory()->create([
        'handle' => Handle::COLLECTION_GROUP_ITINERARIES,
    ]);
});

it('returns collections in the itineraries group', function () {
    LunarCollection::factory()->count(3)->create([
        'collection_group_id' => $this->itinerariesGroup->id,
    ]);

    $result = new GetItineraries()->execute();

    expect($result)->toHaveCount(3);
});

it('does not return collections from other groups', function () {
    $otherGroup = CollectionGroup::factory()->create();
    LunarCollection::factory()->create([
        'collection_group_id' => $otherGroup->id,
    ]);

    $result = new GetItineraries()->execute();

    expect($result)->toBeEmpty();
});

it('returns collections ordered by _lft ascending', function () {
    LunarCollection::factory()->count(3)->create([
        'collection_group_id' => $this->itinerariesGroup->id,
    ]);

    $result = new GetItineraries()->execute();

    $lftValues = $result->pluck('_lft')->all();
    expect($lftValues)->toBe(collect($lftValues)->sort()->values()->all());
});
