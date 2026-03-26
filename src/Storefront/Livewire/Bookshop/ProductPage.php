<?php

namespace Testa\Storefront\Livewire\Bookshop;

use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Contracts\Collection;
use Lunar\Models\Contracts\Product;
use Lunar\Models\Price;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Storefront\Queries\Account\CheckUserHasFavourite;
use Testa\Storefront\Queries\Bookshop\GetProductBySlug;
use Testa\Storefront\UseCases\Account\AddFavouriteProduct;
use Testa\Storefront\UseCases\Account\RemoveFavouriteProduct;

class ProductPage extends Page
{
    public Collection $section;
    public Product $product;
    public ?Price $pricing;
    public bool $isUserFavourite;

    public function mount(string $slug): void
    {
        $this->product = new GetProductBySlug()->execute($slug);

        if ($this->product->getSectionTaxonomy()) {
            $this->section = $this->product->getSectionTaxonomy();
        }

        $this->pricing = $this->product->variant
            ->pricing()
            ->currency(StorefrontSession::getCurrency())
            ->customerGroups(StorefrontSession::getCustomerGroups())
            ->get()->matched;

        if (! Auth::check()) {
            $this->isUserFavourite = false;
        } else {
            $this->isUserFavourite = new CheckUserHasFavourite()->execute(Auth::user(), $this->product->id);
        }
    }

    public function addToFavorites(): void
    {
        if (! Auth::check()) {
            $this->redirect(route('login'), true);
            return;
        }

        $user = Auth::user();

        if (new CheckUserHasFavourite()->execute($user, $this->product->id)) {
            new RemoveFavouriteProduct()->execute($user, $this->product->id);
            $this->isUserFavourite = false;
        } else {
            new AddFavouriteProduct()->execute($user, $this->product->id);
            $this->isUserFavourite = true;
        }
    }

    public function render(): View
    {
        $taxonomies = $this->buildTaxonomies();
        $editorialCollections = $this->filterEditorialCollections($this->product->editorialCollections);

        $synopsis = null;
        if ($this->product->translateAttribute('bookshop-reference')) {
            $synopsis = $this->product->translateAttribute('bookshop-reference');
        } else {
            if ($this->product->translateAttribute('editorial-reference')) {
                $synopsis = $this->product->translateAttribute('editorial-reference');
            }
        }

        $isEditorialProduct = $this->product->brand && $this->product->brand->translateAttribute('in-house') === true;

        return view(
            'testa::storefront.livewire.bookshop.product',
            compact('taxonomies', 'editorialCollections', 'synopsis', 'isEditorialProduct'),
        )->title($this->product->recordFullTitle);
    }

    protected function buildTaxonomies(): SupportCollection
    {
        $items = collect();

        foreach ($this->product->taxonomies as $taxonomy) {
            if ($taxonomy->isInSectionTree() && ! $taxonomy->isRoot()) {
                $wrapper = $taxonomy->getAncestorWrapper();

                if ($wrapper) {
                    $name = $wrapper->translateAttribute('name');
                    $href = $this->sectionHref($wrapper);
                } else {
                    $name = $taxonomy->translateAttribute('name');
                    $href = $this->sectionHref($taxonomy);
                }

                $items->push(['name' => $name, 'href' => $href]);

                continue;
            }

            if (! $taxonomy->isInSectionTree()) {
                $href = $taxonomy->defaultUrl ? route(
                    'testa.storefront.bookshop.topics.show',
                    $taxonomy->defaultUrl->slug,
                ) : null;

                $items->push([
                    'name' => $taxonomy->translateAttribute('name'),
                    'href' => $href,
                ]);
            }
        }

        return $items;
    }

    protected function sectionHref(Collection $taxonomy): ?string
    {
        if ($taxonomy->getAncestorSection() && $taxonomy->getAncestorSection()->defaultUrl) {
            return route(
                'testa.storefront.bookshop.sections.show',
                [
                    'slug' => $taxonomy->getAncestorSection()->defaultUrl->slug,
                    't' => $taxonomy->id,
                ],
            );
        }

        return null;
    }

    private function filterEditorialCollections(SupportCollection $editorialCollections): SupportCollection
    {
        return $editorialCollections->filter(function ($collection) {
            return $collection->translateAttribute('is-section');
        });
    }

}
