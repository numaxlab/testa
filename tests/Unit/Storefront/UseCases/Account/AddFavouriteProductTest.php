<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;
use Testa\Storefront\UseCases\Account\AddFavouriteProduct;
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

it('attaches a product to user favourites', function () {
    $product = Product::factory()->create(['product_type_id' => $this->productType->id]);

    new AddFavouriteProduct()->execute($this->user, $product->id);

    expect($this->user->favourites()->where('product_id', $product->id)->exists())->toBeTrue();
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

    new AddFavouriteProduct()->execute($this->user, $product->id);

    expect($otherUser->favourites()->where('product_id', $product->id)->exists())->toBeFalse();
});
