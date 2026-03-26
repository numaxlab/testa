<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Lunar\Models\Channel;
use Lunar\Models\Collection as LunarCollection;
use Lunar\Models\CollectionGroup;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;
use Testa\Storefront\Queries\Bookshop\GetTopicProducts;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);
    Channel::factory()->create(['default' => true]);
    CustomerGroup::factory()->create(['default' => true]);

    $productType = ProductType::factory()->create();
    config(['lunar.geslib.product_type_id' => $productType->id]);
    $this->productType = $productType;

    $group = CollectionGroup::factory()->create();
    $this->topic = LunarCollection::factory()->create(['collection_group_id' => $group->id]);
});

it('returns a paginator', function () {
    $result = new GetTopicProducts()->execute($this->topic);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
});

it('returns only products belonging to the topic collection', function () {
    $group = CollectionGroup::factory()->create();
    $otherCollection = LunarCollection::factory()->create(['collection_group_id' => $group->id]);

    $matchingProduct = Product::factory()->create([
        'product_type_id' => $this->productType->id,
        'status' => 'published',
    ]);
    $matchingProduct->collections()->attach($this->topic->id);

    $otherProduct = Product::factory()->create([
        'product_type_id' => $this->productType->id,
        'status' => 'published',
    ]);
    $otherProduct->collections()->attach($otherCollection->id);

    $result = new GetTopicProducts()->execute($this->topic);

    expect($result->total())
        ->toBe(1)
        ->and($result->items()[0]->id)->toBe($matchingProduct->id);
});

it('does not return products not attached to the topic', function () {
    Product::factory()->create([
        'product_type_id' => $this->productType->id,
        'status' => 'published',
    ]);

    $result = new GetTopicProducts()->execute($this->topic);

    expect($result->total())->toBe(0);
});

it('paginates with the given perPage value', function () {
    $products = Product::factory()->count(5)->create([
        'product_type_id' => $this->productType->id,
        'status' => 'published',
    ]);
    $products->each(fn($p) => $p->collections()->attach($this->topic->id));

    $result = new GetTopicProducts()->execute($this->topic, '', 3);

    expect($result->perPage())
        ->toBe(3)
        ->and($result->total())->toBe(5);
});

it('defaults to 18 items per page', function () {
    $result = new GetTopicProducts()->execute($this->topic);

    expect($result->perPage())->toBe(18);
});
