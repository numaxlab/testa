<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;
use Lunar\Models\Url;
use Testa\Storefront\Queries\Bookshop\GetProductBySlug;
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
    $this->language = Language::first();
});

function createProductWithSlug(
    ProductType $productType,
    Language $language,
    string $slug,
    string $status = 'published',
): Product {
    $product = Product::factory()->create([
        'product_type_id' => $productType->id,
        'status' => $status,
    ]);

    Url::factory()->create([
        'element_type' => Product::morphName(),
        'element_id' => $product->id,
        'slug' => $slug,
        'default' => true,
        'language_id' => $language->id,
    ]);

    return $product;
}

it('returns the product matching the slug', function () {
    $product = createProductWithSlug($this->productType, $this->language, 'my-book');

    $result = new GetProductBySlug()->execute('my-book');

    expect($result->id)->toBe($product->id);
});

it('throws ModelNotFoundException when slug does not exist', function () {
    new GetProductBySlug()->execute('non-existent-slug');
})->throws(ModelNotFoundException::class);

it('does not return unpublished products', function () {
    createProductWithSlug($this->productType, $this->language, 'draft-book', 'draft');

    new GetProductBySlug()->execute('draft-book');
})->throws(ModelNotFoundException::class);

it('does not return products of a different product type', function () {
    $otherType = ProductType::factory()->create();
    $product = Product::factory()->create([
        'product_type_id' => $otherType->id,
        'status' => 'published',
    ]);
    Url::factory()->create([
        'element_type' => Product::morphName(),
        'element_id' => $product->id,
        'slug' => 'other-type-book',
        'default' => true,
        'language_id' => $this->language->id,
    ]);

    new GetProductBySlug()->execute('other-type-book');
})->throws(ModelNotFoundException::class);

it('eager loads brand relation', function () {
    createProductWithSlug($this->productType, $this->language, 'brand-book');

    $result = new GetProductBySlug()->execute('brand-book');

    expect($result->relationLoaded('brand'))->toBeTrue();
});

it('eager loads media relation', function () {
    createProductWithSlug($this->productType, $this->language, 'media-book');

    $result = new GetProductBySlug()->execute('media-book');

    expect($result->relationLoaded('media'))->toBeTrue();
});
