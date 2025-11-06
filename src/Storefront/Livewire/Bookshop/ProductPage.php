<?php

namespace Trafikrak\Storefront\Livewire\Bookshop;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Price;
use Lunar\Models\Product;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;

class ProductPage extends Page
{
    public \Lunar\Models\Contracts\Collection $section;
    public Product $product;
    public ?Price $pricing;
    public Collection $itineraries;
    public bool $isUserFavourite;

    public function mount(string $slug): void
    {
        $this->product = Product::channel(StorefrontSession::getChannel())
            ->customerGroup(StorefrontSession::getCustomerGroups())
            ->status('published')
            ->whereHas('productType', function ($query) {
                $query->where('id', config('lunar.geslib.product_type_id'));
            })
            ->whereHas('urls', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })
            ->with([
                'variant',
                'variant.prices',
                'variant.prices.priceable',
                'variant.prices.priceable.taxClass',
                'variant.prices.priceable.taxClass.taxRateAmounts',
                'variant.prices.currency',
                'media',
                'taxonomies',
                'taxonomies.ancestors',
                'editorialCollections',
                'languages',
                'statuses',
            ])
            ->firstOrFail();

        if ($this->product->getSectionTaxonomy()) {
            $this->section = $this->product->getSectionTaxonomy();
        }

        $this->pricing = $this->product->variant
            ->pricing()
            ->currency(StorefrontSession::getCurrency())
            ->customerGroups(StorefrontSession::getCustomerGroups())
            ->get()->matched;

        if (!Auth::check()) {
            $this->isUserFavourite = false;
        } else {
            $user = Auth::user();

            $this->isUserFavourite = $user->favourites->contains($this->product->id);
        }
    }

    public function addToFavorites(): null
    {
        if (!Auth::check()) {
            return $this->redirect(route('login'), true);
        }

        $user = Auth::user();

        if ($user->favourites->contains($this->product->id)) {
            $user->favourites()->detach($this->product->id);
            $this->isUserFavourite = false;
        } else {
            $user->favourites()->attach($this->product->id);
            $this->isUserFavourite = true;
        }

        return null;
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.bookshop.product')
            ->title($this->product->recordFullTitle);
    }
}
