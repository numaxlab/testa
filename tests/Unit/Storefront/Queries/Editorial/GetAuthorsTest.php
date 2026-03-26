<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Lunar\FieldTypes\Toggle;
use Lunar\Models\Brand;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;
use NumaxLab\Geslib\Lines\AuthorType;
use NumaxLab\Lunar\Geslib\Models\Author;
use Testa\Storefront\Queries\Editorial\GetAuthors;
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

    $this->inHouseBrand = Brand::factory()->create([
        'attribute_data' => collect(['in-house' => new Toggle(true)]),
    ]);
    $this->externalBrand = Brand::factory()->create([
        'attribute_data' => collect(['in-house' => new Toggle(false)]),
    ]);
});

function attachAuthorToProduct(Product $product, Author $author): void
{
    $product->contributors()->attach($author->id, [
        'author_type' => AuthorType::AUTHOR,
        'position' => 1,
    ]);
}

it('returns a paginator', function () {
    $result = new GetAuthors()->execute();

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
});

it('returns authors who have products with an in-house brand', function () {
    $author = Author::factory()->create();
    $product = Product::factory()->create([
        'product_type_id' => $this->productType->id,
        'brand_id' => $this->inHouseBrand->id,
    ]);
    attachAuthorToProduct($product, $author);

    $result = new GetAuthors()->execute();

    expect($result->total())
        ->toBe(1)
        ->and($result->items()[0]->id)->toBe($author->id);
});

it('excludes authors who only have products with external brands', function () {
    $author = Author::factory()->create();
    $product = Product::factory()->create([
        'product_type_id' => $this->productType->id,
        'brand_id' => $this->externalBrand->id,
    ]);
    attachAuthorToProduct($product, $author);

    $result = new GetAuthors()->execute();

    expect($result->total())->toBe(0);
});

it('excludes authors with no products', function () {
    Author::factory()->create();

    $result = new GetAuthors()->execute();

    expect($result->total())->toBe(0);
});

it('returns results ordered alphabetically by name', function () {
    $authorC = Author::factory()->create(['name' => 'Carmen']);
    $authorA = Author::factory()->create(['name' => 'Ana']);
    $authorB = Author::factory()->create(['name' => 'Beatriz']);

    foreach ([$authorA, $authorB, $authorC] as $author) {
        $product = Product::factory()->create([
            'product_type_id' => $this->productType->id,
            'brand_id' => $this->inHouseBrand->id,
        ]);
        attachAuthorToProduct($product, $author);
    }

    $result = new GetAuthors()->execute();

    $names = collect($result->items())->pluck('name')->values()->all();
    expect($names)->toBe(['Ana', 'Beatriz', 'Carmen']);
});

it('defaults to 32 items per page', function () {
    $result = new GetAuthors()->execute();

    expect($result->perPage())->toBe(32);
});

it('paginates with the given perPage value', function () {
    foreach (range(1, 5) as $i) {
        $author = Author::factory()->create(['name' => "Author {$i}"]);
        $product = Product::factory()->create([
            'product_type_id' => $this->productType->id,
            'brand_id' => $this->inHouseBrand->id,
        ]);
        attachAuthorToProduct($product, $author);
    }

    $result = new GetAuthors()->execute(2);

    expect($result->perPage())
        ->toBe(2)
        ->and($result->total())->toBe(5);
});
