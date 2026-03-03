<?php

use Illuminate\Support\Facades\Schema;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;
use Testa\Storefront\Livewire\Account\FavouriteProductsPage;

use function Pest\Livewire\livewire;

beforeEach(function () {
    Schema::table('users', function ($table) {
        $table->dropColumn('name');
        $table->string('first_name')->after('id');
        $table->string('last_name')->after('first_name');
    });

    config(['auth.providers.users.model' => \Testa\Tests\Stubs\User::class]);

    $this->language = Language::factory()->create(['default' => true]);
    $this->currency = Currency::factory()->create(['default' => true, 'decimal_places' => 2]);
    $this->channel = Channel::factory()->create(['default' => true]);
    $this->taxClass = TaxClass::factory()->create(['default' => true]);
    $this->customerGroup = CustomerGroup::factory()->create(['default' => true]);
    $this->productType = ProductType::factory()->create();

    $userModel = config('auth.providers.users.model');
    $userModel::unguard();
    $this->user = $userModel::create([
        'first_name' => 'Fav',
        'last_name' => 'User',
        'email' => 'fav@example.com',
        'password' => bcrypt('password'),
    ]);
    $userModel::reguard();

    $this->actingAs($this->user);
});

describe('render', function () {
    it('renders successfully with no favourites', function () {
        livewire(FavouriteProductsPage::class)
            ->assertOk();
    });

    it('renders successfully with favourite products', function () {
        $product = Product::factory()->create(['product_type_id' => $this->productType->id]);
        $this->user->favourites()->attach($product->id);

        livewire(FavouriteProductsPage::class)
            ->assertOk();
    });
});

describe('removeFromFavourites', function () {
    it('removes product from user favourites', function () {
        $product = Product::factory()->create(['product_type_id' => $this->productType->id]);
        $this->user->favourites()->attach($product->id);

        expect($this->user->favourites()->count())->toBe(1);

        livewire(FavouriteProductsPage::class)
            ->call('removeFromFavourites', $product->id);

        expect($this->user->favourites()->count())->toBe(0);
    });

    it('dispatches $refresh event after removing', function () {
        $product = Product::factory()->create(['product_type_id' => $this->productType->id]);
        $this->user->favourites()->attach($product->id);

        livewire(FavouriteProductsPage::class)
            ->call('removeFromFavourites', $product->id)
            ->assertDispatched('$refresh');
    });
});
