<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;
use Testa\Storefront\UseCases\Account\RemoveFavouriteProduct;
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

it('detaches the product from the user favourites', function () {
    $product = Product::factory()->create(['product_type_id' => $this->productType->id]);
    $this->user->favourites()->attach($product->id);

    expect($this->user->favourites()->count())->toBe(1);

    (new RemoveFavouriteProduct())->execute($this->user, $product->id);

    expect($this->user->favourites()->count())->toBe(0);
});

it('does not affect other users favourites', function () {
    $userModel = config('auth.providers.users.model');
    $userModel::unguard();
    $otherUser = $userModel::create([
        'first_name' => 'Other',
        'last_name' => 'User',
        'email' => 'other@example.com',
        'password' => bcrypt('password'),
    ]);
    $userModel::reguard();

    $product = Product::factory()->create(['product_type_id' => $this->productType->id]);
    $this->user->favourites()->attach($product->id);
    $otherUser->favourites()->attach($product->id);

    (new RemoveFavouriteProduct())->execute($this->user, $product->id);

    expect($this->user->favourites()->count())
        ->toBe(0)
        ->and($otherUser->favourites()->count())->toBe(1);
});

it('does nothing when the product is not in the user favourites', function () {
    $product = Product::factory()->create(['product_type_id' => $this->productType->id]);

    expect(fn() => (new RemoveFavouriteProduct())->execute($this->user, $product->id))
        ->not->toThrow(Exception::class);

    expect($this->user->favourites()->count())->toBe(0);
});
