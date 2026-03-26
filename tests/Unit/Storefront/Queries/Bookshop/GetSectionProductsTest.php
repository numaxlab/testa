<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
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
use Testa\Storefront\Queries\Bookshop\GetSectionProducts;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Manually insert a child collection into the nested set under $parent.
 * Kalnoy's appendToNode has a bug in this test environment, so we
 * implement the gap-and-insert algorithm directly.
 */
function insertChildCollection(LunarCollection $parent, CollectionGroup $group): LunarCollection
{
    $insertPoint = $parent->_rgt;

    DB::table('lunar_collections')
        ->where('_lft', '>=', $insertPoint)
        ->increment('_lft', 2);
    DB::table('lunar_collections')
        ->where('_rgt', '>=', $insertPoint)
        ->increment('_rgt', 2);

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
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);
    Channel::factory()->create(['default' => true]);
    CustomerGroup::factory()->create(['default' => true]);

    $productType = ProductType::factory()->create();
    config(['lunar.geslib.product_type_id' => $productType->id]);
    $this->productType = $productType;

    $this->group = CollectionGroup::factory()->create();
    $this->section = LunarCollection::factory()->create([
        'collection_group_id' => $this->group->id,
    ]);
});

it('returns a paginator', function () {
    $result = new GetSectionProducts()->execute($this->section);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
});

it('returns products in the section descendants by default', function () {
    $child = insertChildCollection($this->section, $this->group);

    $product = Product::factory()->create([
        'product_type_id' => $this->productType->id,
        'status' => 'published',
    ]);
    $product->collections()->attach($child->id);

    $result = new GetSectionProducts()->execute($this->section);

    expect($result->total())
        ->toBe(1)
        ->and($result->items()[0]->id)->toBe($product->id);
});

it('does not return products outside the section', function () {
    $otherCollection = LunarCollection::factory()->create([
        'collection_group_id' => $this->group->id,
    ]);

    $product = Product::factory()->create([
        'product_type_id' => $this->productType->id,
        'status' => 'published',
    ]);
    $product->collections()->attach($otherCollection->id);

    $result = new GetSectionProducts()->execute($this->section);

    expect($result->total())->toBe(0);
});

it('filters by a specific leaf collection when t is given', function () {
    $child = insertChildCollection($this->section, $this->group);
    $other = insertChildCollection($this->section, $this->group);

    $matchingProduct = Product::factory()->create([
        'product_type_id' => $this->productType->id,
        'status' => 'published',
    ]);
    $matchingProduct->collections()->attach($child->id);

    $otherProduct = Product::factory()->create([
        'product_type_id' => $this->productType->id,
        'status' => 'published',
    ]);
    $otherProduct->collections()->attach($other->id);

    $result = new GetSectionProducts()->execute($this->section, '', (string) $child->id);

    expect($result->total())
        ->toBe(1)
        ->and($result->items()[0]->id)->toBe($matchingProduct->id);
});

it('filters by descendants when t points to a parent collection', function () {
    $parent = insertChildCollection($this->section, $this->group);
    $grandchild = insertChildCollection($parent, $this->group);

    $productInGrandchild = Product::factory()->create([
        'product_type_id' => $this->productType->id,
        'status' => 'published',
    ]);
    $productInGrandchild->collections()->attach($grandchild->id);

    $productInParent = Product::factory()->create([
        'product_type_id' => $this->productType->id,
        'status' => 'published',
    ]);
    $productInParent->collections()->attach($parent->id);

    $result = new GetSectionProducts()->execute($this->section, '', (string) $parent->id);

    // Only products in descendants (grandchild), not in the parent itself
    expect($result->total())
        ->toBe(1)
        ->and($result->items()[0]->id)->toBe($productInGrandchild->id);
});

it('paginates with the given perPage value', function () {
    $child = insertChildCollection($this->section, $this->group);

    $products = Product::factory()->count(5)->create([
        'product_type_id' => $this->productType->id,
        'status' => 'published',
    ]);
    $products->each(fn($p) => $p->collections()->attach($child->id));

    $result = new GetSectionProducts()->execute($this->section, '', '', 3);

    expect($result->perPage())
        ->toBe(3)
        ->and($result->total())->toBe(5);
});
