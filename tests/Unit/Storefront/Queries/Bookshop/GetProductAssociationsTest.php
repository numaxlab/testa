<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Channel;
use Lunar\Models\Collection as LunarCollection;
use Lunar\Models\CollectionGroup;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductAssociation;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;
use NumaxLab\Geslib\Lines\AuthorType;
use NumaxLab\Lunar\Geslib\InterCommands\CollectionCommand;
use NumaxLab\Lunar\Geslib\Models\Author;
use Testa\Storefront\Queries\Bookshop\GetProductAssociations;
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

    $this->product = Product::factory()->create([
        'product_type_id' => $productType->id,
        'status' => 'published',
    ]);
});

// --- manual() ---

it('manual returns an empty collection when product has no associations', function () {
    $result = new GetProductAssociations()->manual($this->product);

    expect($result)->toBeEmpty();
});

it('manual returns product associations', function () {
    $target = Product::factory()->create(['product_type_id' => $this->productType->id, 'status' => 'published']);
    ProductAssociation::factory()->create([
        'product_parent_id' => $this->product->id,
        'product_target_id' => $target->id,
    ]);

    $result = new GetProductAssociations()->manual($this->product);

    expect($result)->toHaveCount(1);
});

it('manual does not return associations from other products', function () {
    $other = Product::factory()->create(['product_type_id' => $this->productType->id]);
    $target = Product::factory()->create(['product_type_id' => $this->productType->id]);
    ProductAssociation::factory()->create([
        'product_parent_id' => $other->id,
        'product_target_id' => $target->id,
    ]);

    $result = new GetProductAssociations()->manual($this->product);

    expect($result)->toBeEmpty();
});

// --- automatic() ---

it('automatic returns an empty collection when manual associations fill the limit', function () {
    $manualAssociations = collect(range(1, 6))->map(fn() => new \stdClass());

    $result = new GetProductAssociations()->automatic($this->product, false, $manualAssociations);

    expect($result)->toBeEmpty();
});

it('automatic returns products sharing the same author', function () {
    $author = Author::factory()->create();
    $related = Product::factory()->create(['product_type_id' => $this->productType->id, 'status' => 'published']);

    $this->product->contributors()->attach($author->id, ['author_type' => AuthorType::AUTHOR, 'position' => 1]);
    $related->contributors()->attach($author->id, ['author_type' => AuthorType::AUTHOR, 'position' => 1]);

    $result = new GetProductAssociations()->automatic($this->product, false, collect());

    expect($result->pluck('id')->contains($related->id))->toBeTrue();
});

it('automatic does not include the product itself', function () {
    $author = Author::factory()->create();
    $this->product->contributors()->attach($author->id, ['author_type' => AuthorType::AUTHOR, 'position' => 1]);

    $result = new GetProductAssociations()->automatic($this->product, false, collect());

    expect($result->pluck('id')->contains($this->product->id))->toBeFalse();
});

it('automatic returns products sharing an editorial collection', function () {
    $group = CollectionGroup::factory()->create(['handle' => CollectionCommand::HANDLE]);
    $collection = LunarCollection::factory()->create(['collection_group_id' => $group->id]);
    $related = Product::factory()->create(['product_type_id' => $this->productType->id, 'status' => 'published']);

    $collection->products()->attach($this->product->id);
    $collection->products()->attach($related->id);

    $result = new GetProductAssociations()->automatic($this->product, false, collect());

    expect($result->pluck('id')->contains($related->id))->toBeTrue();
});

it('automatic does not return the same product twice across relationships', function () {
    // A product sharing both an author and an editorial collection should appear only once
    $author = Author::factory()->create();
    $group = CollectionGroup::factory()->create(['handle' => CollectionCommand::HANDLE]);
    $collection = LunarCollection::factory()->create(['collection_group_id' => $group->id]);

    $related = Product::factory()->create(['product_type_id' => $this->productType->id, 'status' => 'published']);

    $this->product->contributors()->attach($author->id, ['author_type' => AuthorType::AUTHOR, 'position' => 1]);
    $related->contributors()->attach($author->id, ['author_type' => AuthorType::AUTHOR, 'position' => 1]);

    $collection->products()->attach($this->product->id);
    $collection->products()->attach($related->id);

    $result = new GetProductAssociations()->automatic($this->product, false, collect());

    expect($result->where('id', $related->id)->count())->toBe(1);
});

it('automatic returns an empty collection when product has no shared relationships', function () {
    $result = new GetProductAssociations()->automatic($this->product, false, collect());

    expect($result)->toBeEmpty();
});
