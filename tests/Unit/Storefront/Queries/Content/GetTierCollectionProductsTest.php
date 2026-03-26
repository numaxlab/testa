<?php

use Illuminate\Database\Eloquent\Collection;
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
use Testa\Models\Content\Tier;
use Testa\Storefront\Queries\Content\GetTierCollectionProducts;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create(['default' => true]);
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);
    Channel::factory()->create(['default' => true]);
    CustomerGroup::factory()->create(['default' => true]);

    $productType = ProductType::factory()->create();
    config(['lunar.geslib.product_type_id' => $productType->id]);
    $this->productType = $productType;

    $group = CollectionGroup::factory()->create();
    $this->collection = LunarCollection::factory()->create(['collection_group_id' => $group->id]);
    $this->tier = Tier::factory()->create();
    $this->tier->collections()->attach($this->collection->id);
});

it('returns published products in the tier collections', function () {
    $product = Product::factory()->create(['product_type_id' => $this->productType->id, 'status' => 'published']);
    $product->collections()->attach($this->collection->id);

    $result = new GetTierCollectionProducts()->execute($this->tier->fresh(['collections']));

    expect($result->pluck('id')->contains($product->id))->toBeTrue();
});

it('excludes products not in tier collections', function () {
    $otherGroup = CollectionGroup::factory()->create();
    $otherCollection = LunarCollection::factory()->create(['collection_group_id' => $otherGroup->id]);
    $product = Product::factory()->create(['product_type_id' => $this->productType->id, 'status' => 'published']);
    $product->collections()->attach($otherCollection->id);

    $result = new GetTierCollectionProducts()->execute($this->tier->fresh(['collections']));

    expect($result)->toBeEmpty();
});

it('excludes unpublished products', function () {
    $product = Product::factory()->create(['product_type_id' => $this->productType->id, 'status' => 'draft']);
    $product->collections()->attach($this->collection->id);

    $result = new GetTierCollectionProducts()->execute($this->tier->fresh(['collections']));

    expect($result)->toBeEmpty();
});

it('limits results to 12', function () {
    $products = Product::factory()->count(15)->create([
        'product_type_id' => $this->productType->id, 'status' => 'published',
    ]);
    $products->each(fn($p) => $p->collections()->attach($this->collection->id));

    $result = new GetTierCollectionProducts()->execute($this->tier->fresh(['collections']));

    expect($result)->toHaveCount(12);
});

it('returns an empty collection when tier collections have no products', function () {
    $result = new GetTierCollectionProducts()->execute($this->tier->fresh(['collections']));

    expect($result)->toBeInstanceOf(Collection::class)->toBeEmpty();
});
