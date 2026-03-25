<?php

namespace Testa\Storefront\Livewire\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Livewire\Features\WithPagination;
use Testa\Storefront\Queries\Account\GetUserFavouriteProducts;
use Testa\Storefront\UseCases\Account\RemoveFavouriteProduct;

class FavouriteProductsPage extends Page
{
    use WithPagination;

    public function removeFromFavourites($productId): void
    {
        new RemoveFavouriteProduct()->execute(Auth::user(), (int) $productId);

        $this->dispatch('$refresh');
    }

    public function render(): View
    {
        $favouriteProducts = new GetUserFavouriteProducts()->execute(Auth::user());

        return view('testa::storefront.livewire.account.favourite-products', compact('favouriteProducts'));
    }
}
