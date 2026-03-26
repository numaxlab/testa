<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Channel;
use Lunar\Models\Collection as LunarCollection;
use Lunar\Models\CollectionGroup;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use NumaxLab\Lunar\Geslib\InterCommands\StatusCommand;
use Testa\Storefront\Queries\Bookshop\GetStatusCollections;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);
    Channel::factory()->create(['default' => true]);
    CustomerGroup::factory()->create(['default' => true]);

    $this->statusGroup = CollectionGroup::factory()->create([
        'handle' => StatusCommand::HANDLE,
    ]);
});

it('returns collections in the status group', function () {
    LunarCollection::factory()->count(3)->create([
        'collection_group_id' => $this->statusGroup->id,
    ]);

    $result = new GetStatusCollections()->execute();

    expect($result)->toHaveCount(3);
});

it('does not return collections from other groups', function () {
    $otherGroup = CollectionGroup::factory()->create();
    LunarCollection::factory()->create([
        'collection_group_id' => $otherGroup->id,
    ]);

    $result = new GetStatusCollections()->execute();

    expect($result)->toBeEmpty();
});
