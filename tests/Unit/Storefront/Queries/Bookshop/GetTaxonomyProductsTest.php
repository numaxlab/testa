<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Lunar\FieldTypes\Text;
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
use Testa\Storefront\Queries\Bookshop\GetTaxonomyProducts;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function insertTaxonomyChild(LunarCollection $parent, CollectionGroup $group): LunarCollection
{
    $insertPoint = $parent->_rgt;

    DB::table('lunar_collections')->where('_lft', '>=', $insertPoint)->increment('_lft', 2);
    DB::table('lunar_collections')->where('_rgt', '>=', $insertPoint)->increment('_rgt', 2);

    $child = LunarCollection::withoutEvents(function () use ($parent, $group, $insertPoint) {
        return LunarCollection::forceCreate([
            'collection_group_id' => $group->id,
            'attribute_data' => collect(['name' => new Text('Child')]),
            '_lft' => $insertPoint,
            '_rgt' => $insertPoint + 1,
            'parent_id' => $parent->id,
        ]);
    });

    $parent->refresh();

    return $child;
}

beforeEach(function () {
    Language::factory()->create(['default' => true]);
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);
    Channel::factory()->create(['default' => true]);
    CustomerGroup::factory()->create(['default' => true]);

    $productType = ProductType::factory()->create();
    config(['lunar.geslib.product_type_id' => $productType->id]);
    $this->productType = $productType;

    $this->group = CollectionGroup::factory()->create(['handle' => Handle::COLLECTION_GROUP_TAXONOMIES]);
    $this->collection = LunarCollection::factory()->create(['collection_group_id' => $this->group->id]);
});

it('returns products in the collection', function () {
    $product = Product::factory()->create(['product_type_id' => $this->productType->id, 'status' => 'published']);
    $this->collection->products()->attach($product->id);

    $result = new GetTaxonomyProducts()->execute($this->collection);

    expect($result)
        ->toHaveCount(1)
        ->and($result->first()->id)->toBe($product->id);
});

it('returns products in descendant collections', function () {
    $child = insertTaxonomyChild($this->collection, $this->group);
    $product = Product::factory()->create(['product_type_id' => $this->productType->id, 'status' => 'published']);
    $child->products()->attach($product->id);

    $result = new GetTaxonomyProducts()->execute($this->collection->fresh());

    expect($result->pluck('id')->contains($product->id))->toBeTrue();
});

it('does not return products from unrelated collections', function () {
    $other = LunarCollection::factory()->create(['collection_group_id' => $this->group->id]);
    $product = Product::factory()->create(['product_type_id' => $this->productType->id, 'status' => 'published']);
    $other->products()->attach($product->id);

    $result = new GetTaxonomyProducts()->execute($this->collection);

    expect($result)->toBeEmpty();
});

it('limits results to the given limit', function () {
    $products = Product::factory()->count(10)->create([
        'product_type_id' => $this->productType->id,
        'status' => 'published',
    ]);
    $products->each(fn($p) => $this->collection->products()->attach($p->id));

    $result = new GetTaxonomyProducts()->execute($this->collection, 4);

    expect($result)->toHaveCount(4);
});

it('defaults to a limit of 6', function () {
    $products = Product::factory()->count(10)->create([
        'product_type_id' => $this->productType->id,
        'status' => 'published',
    ]);
    $products->each(fn($p) => $this->collection->products()->attach($p->id));

    $result = new GetTaxonomyProducts()->execute($this->collection);

    expect($result)->toHaveCount(6);
});

it('returns an empty collection when no products are in the taxonomy', function () {
    $result = new GetTaxonomyProducts()->execute($this->collection);

    expect($result)->toBeEmpty();
});
