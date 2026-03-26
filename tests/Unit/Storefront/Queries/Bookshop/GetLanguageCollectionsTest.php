<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Channel;
use Lunar\Models\Collection as LunarCollection;
use Lunar\Models\CollectionGroup;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use NumaxLab\Lunar\Geslib\InterCommands\LanguageCommand;
use Testa\Storefront\Queries\Bookshop\GetLanguageCollections;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);
    Channel::factory()->create(['default' => true]);
    CustomerGroup::factory()->create(['default' => true]);

    $this->languageGroup = CollectionGroup::factory()->create([
        'handle' => LanguageCommand::HANDLE,
    ]);
});

it('returns collections in the language group', function () {
    LunarCollection::factory()->count(3)->create([
        'collection_group_id' => $this->languageGroup->id,
    ]);

    $result = new GetLanguageCollections()->execute();

    expect($result)->toHaveCount(3);
});

it('does not return collections from other groups', function () {
    $otherGroup = CollectionGroup::factory()->create();
    LunarCollection::factory()->create([
        'collection_group_id' => $otherGroup->id,
    ]);

    $result = new GetLanguageCollections()->execute();

    expect($result)->toBeEmpty();
});
