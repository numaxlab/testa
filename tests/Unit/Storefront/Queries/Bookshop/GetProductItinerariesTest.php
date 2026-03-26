<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Channel;
use Lunar\Models\Collection as LunarCollection;
use Lunar\Models\CollectionGroup;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;
use NumaxLab\Lunar\Geslib\Handle;
use Testa\Storefront\Queries\Bookshop\GetProductItineraries;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create(['default' => true]);
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);
    Channel::factory()->create(['default' => true]);
    CustomerGroup::factory()->create(['default' => true]);

    $productType = ProductType::factory()->create();
    $this->product = Product::factory()->create(['product_type_id' => $productType->id]);

    $this->itinerariesGroup = CollectionGroup::factory()->create([
        'handle' => Handle::COLLECTION_GROUP_ITINERARIES,
    ]);
});

it('returns itineraries containing the product', function () {
    $itinerary = LunarCollection::factory()->create(['collection_group_id' => $this->itinerariesGroup->id]);
    $itinerary->products()->attach($this->product->id);

    $result = new GetProductItineraries()->execute($this->product);

    expect($result)
        ->toHaveCount(1)
        ->and($result->first()->id)->toBe($itinerary->id);
});

it('does not return itineraries that do not contain the product', function () {
    LunarCollection::factory()->create(['collection_group_id' => $this->itinerariesGroup->id]);

    $result = new GetProductItineraries()->execute($this->product);

    expect($result)->toBeEmpty();
});

it('does not return collections from other groups', function () {
    $otherGroup = CollectionGroup::factory()->create();
    $collection = LunarCollection::factory()->create(['collection_group_id' => $otherGroup->id]);
    $collection->products()->attach($this->product->id);

    $result = new GetProductItineraries()->execute($this->product);

    expect($result)->toBeEmpty();
});

it('does not return itineraries containing only other products', function () {
    $otherProduct = Product::factory()->create(['product_type_id' => $this->product->product_type_id]);
    $itinerary = LunarCollection::factory()->create(['collection_group_id' => $this->itinerariesGroup->id]);
    $itinerary->products()->attach($otherProduct->id);

    $result = new GetProductItineraries()->execute($this->product);

    expect($result)->toBeEmpty();
});

it('returns an empty collection when product has no itineraries', function () {
    $result = new GetProductItineraries()->execute($this->product);

    expect($result)->toBeEmpty();
});
