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
use Testa\Storefront\Queries\Bookshop\SearchTaxonomies;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    config(['scout.driver' => 'collection']);

    Language::factory()->create(['default' => true]);
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);
    Channel::factory()->create(['default' => true]);
    CustomerGroup::factory()->create(['default' => true]);

    $this->taxonomiesGroup = CollectionGroup::factory()->create([
        'handle' => Handle::COLLECTION_GROUP_TAXONOMIES,
    ]);
});

it('returns taxonomy collections', function () {
    LunarCollection::factory()->count(3)->create(['collection_group_id' => $this->taxonomiesGroup->id]);

    $result = new SearchTaxonomies()->execute('');

    expect($result)->toHaveCount(3);
});

it('does not return collections from other groups', function () {
    $otherGroup = CollectionGroup::factory()->create();
    LunarCollection::factory()->create(['collection_group_id' => $otherGroup->id]);

    $result = new SearchTaxonomies()->execute('');

    expect($result)->toBeEmpty();
});

it('returns an empty collection when no taxonomies exist', function () {
    $result = new SearchTaxonomies()->execute('');

    expect($result)->toBeEmpty();
});
