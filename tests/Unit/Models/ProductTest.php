<?php

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Language;
use Testa\Models\Product;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(fn() => Language::factory()->create());

it('has reviews relationship', function () {
    $product = new Product();
    expect($product->reviews())->toBeInstanceOf(HasMany::class);
});

it('has courses relationship', function () {
    $product = new Product();
    expect($product->courses())->toBeInstanceOf(BelongsToMany::class);
});
