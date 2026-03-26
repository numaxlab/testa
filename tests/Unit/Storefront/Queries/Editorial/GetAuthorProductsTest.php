<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;
use NumaxLab\Geslib\Lines\AuthorType;
use NumaxLab\Lunar\Geslib\Models\Author;
use Testa\Storefront\Queries\Editorial\GetAuthorProducts;
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

    $this->author = Author::factory()->create();
});

function attachAuthor(Product $product, Author $author): void
{
    $product->contributors()->attach($author->id, [
        'author_type' => AuthorType::AUTHOR,
        'position' => 1,
    ]);
}

it('returns a paginator', function () {
    $result = new GetAuthorProducts()->execute($this->author);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
});

it('returns only products associated with the given author', function () {
    $ownProduct = Product::factory()->create([
        'product_type_id' => $this->productType->id,
        'status' => 'published',
    ]);
    attachAuthor($ownProduct, $this->author);

    $otherProduct = Product::factory()->create([
        'product_type_id' => $this->productType->id,
        'status' => 'published',
    ]);

    $result = new GetAuthorProducts()->execute($this->author);

    expect($result->total())
        ->toBe(1)
        ->and($result->items()[0]->id)->toBe($ownProduct->id);
});

it('does not return products from other authors', function () {
    $otherAuthor = Author::factory()->create();
    $product = Product::factory()->create([
        'product_type_id' => $this->productType->id,
        'status' => 'published',
    ]);
    attachAuthor($product, $otherAuthor);

    $result = new GetAuthorProducts()->execute($this->author);

    expect($result->total())->toBe(0);
});

it('paginates with the given perPage value', function () {
    $products = Product::factory()->count(10)->create([
        'product_type_id' => $this->productType->id,
        'status' => 'published',
    ]);
    $products->each(fn($p) => attachAuthor($p, $this->author));

    $result = new GetAuthorProducts()->execute($this->author, 3);

    expect($result->perPage())
        ->toBe(3)
        ->and($result->total())->toBe(10);
});

it('defaults to 12 items per page', function () {
    $result = new GetAuthorProducts()->execute($this->author);

    expect($result->perPage())->toBe(12);
});
