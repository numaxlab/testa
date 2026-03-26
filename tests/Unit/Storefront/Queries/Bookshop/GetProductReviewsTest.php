<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;
use Testa\Models\Editorial\Review;
use Testa\Storefront\Queries\Bookshop\GetProductReviews;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create(['default' => true]);
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);

    $productType = ProductType::factory()->create();
    $this->product = Product::factory()->create(['product_type_id' => $productType->id]);
});

it('returns reviews for the product', function () {
    Review::factory()->count(3)->create(['product_id' => $this->product->id]);

    $result = new GetProductReviews()->execute($this->product);

    expect($result)->toHaveCount(3);
});

it('does not return reviews from other products', function () {
    $otherProduct = Product::factory()->create(['product_type_id' => $this->product->product_type_id]);
    Review::factory()->create(['product_id' => $otherProduct->id]);

    $result = new GetProductReviews()->execute($this->product);

    expect($result)->toBeEmpty();
});

it('returns an empty collection when product has no reviews', function () {
    $result = new GetProductReviews()->execute($this->product);

    expect($result)->toBeEmpty();
});
