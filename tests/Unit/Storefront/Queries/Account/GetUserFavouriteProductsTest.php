<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;
use Testa\Storefront\Queries\Account\GetUserFavouriteProducts;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Schema::table('users', function ($table) {
        $table->dropColumn('name');
        $table->string('first_name')->after('id');
        $table->string('last_name')->after('first_name');
    });

    config(['auth.providers.users.model' => \Testa\Tests\Stubs\User::class]);

    Language::factory()->create(['default' => true]);
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);

    $this->productType = ProductType::factory()->create();

    $userModel = config('auth.providers.users.model');
    $userModel::unguard();
    $this->user = $userModel::create([
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
    $userModel::reguard();
});

it('returns a paginator', function () {
    $result = (new GetUserFavouriteProducts())->execute($this->user);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
});

it('returns only the favourites of the given user', function () {
    $userModel = config('auth.providers.users.model');
    $userModel::unguard();
    $otherUser = $userModel::create([
        'first_name' => 'Other',
        'last_name' => 'User',
        'email' => 'other@example.com',
        'password' => bcrypt('password'),
    ]);
    $userModel::reguard();

    $ownProduct = Product::factory()->create(['product_type_id' => $this->productType->id]);
    $otherProduct = Product::factory()->create(['product_type_id' => $this->productType->id]);

    $this->user->favourites()->attach($ownProduct->id);
    $otherUser->favourites()->attach($otherProduct->id);

    $result = (new GetUserFavouriteProducts())->execute($this->user);

    expect($result->total())
        ->toBe(1)
        ->and($result->items()[0]->id)->toBe($ownProduct->id);
});

it('paginates with the given perPage value', function () {
    $products = Product::factory()->count(10)->create(['product_type_id' => $this->productType->id]);
    $this->user->favourites()->attach($products->pluck('id'));

    $result = (new GetUserFavouriteProducts())->execute($this->user, 3);

    expect($result->perPage())
        ->toBe(3)
        ->and($result->total())->toBe(10);
});

it('defaults to 12 items per page', function () {
    $result = (new GetUserFavouriteProducts())->execute($this->user);

    expect($result->perPage())->toBe(12);
});
