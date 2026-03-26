<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;
use Testa\Storefront\Queries\Account\GetUserLatestFavouriteProducts;
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

it('returns favourite products for the user', function () {
    $product = Product::factory()->create(['product_type_id' => $this->productType->id]);
    $this->user->favourites()->attach($product->id);

    $result = (new GetUserLatestFavouriteProducts())->execute($this->user);

    expect($result)
        ->toHaveCount(1)
        ->and($result->first()->id)->toBe($product->id);
});

it('limits results to the given limit', function () {
    Product::factory()->count(5)->create(['product_type_id' => $this->productType->id])
        ->each(fn($p) => $this->user->favourites()->attach($p->id));

    $result = (new GetUserLatestFavouriteProducts())->execute($this->user, 3);

    expect($result)->toHaveCount(3);
});

it('uses a default limit of 3', function () {
    Product::factory()->count(5)->create(['product_type_id' => $this->productType->id])
        ->each(fn($p) => $this->user->favourites()->attach($p->id));

    $result = (new GetUserLatestFavouriteProducts())->execute($this->user);

    expect($result)->toHaveCount(3);
});

it('returns an empty collection when user has no favourites', function () {
    $result = (new GetUserLatestFavouriteProducts())->execute($this->user);

    expect($result)->toBeEmpty();
});
