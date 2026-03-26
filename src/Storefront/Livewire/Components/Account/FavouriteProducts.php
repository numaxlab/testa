<?php

namespace Testa\Storefront\Livewire\Components\Account;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Testa\Storefront\Queries\Account\GetUserLatestFavouriteProducts;
use Testa\Storefront\UseCases\Account\RemoveFavouriteProduct;

class FavouriteProducts extends Component
{
    public Collection $latestFavouriteProducts;

    public function mount(): void
    {
        $this->latestFavouriteProducts = new GetUserLatestFavouriteProducts()->execute(Auth::user());
    }

    public function removeFromFavourites($productId): void
    {
        new RemoveFavouriteProduct()->execute(Auth::user(), $productId);

        $this->latestFavouriteProducts = new GetUserLatestFavouriteProducts()->execute(Auth::user());
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.account.favourite-products');
    }
}
