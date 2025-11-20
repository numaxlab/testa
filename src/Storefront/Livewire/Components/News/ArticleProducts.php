<?php

namespace Trafikrak\Storefront\Livewire\Components\News;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Facades\StorefrontSession;
use Trafikrak\Models\News\Article;

class ArticleProducts extends Component
{
    public Article $article;

    public Collection $products;

    public function mount(): void
    {
        $this->products = $this->article
            ->products()->channel(StorefrontSession::getChannel())
            ->customerGroup(StorefrontSession::getCustomerGroups())
            ->status('published')
            ->whereHas('productType', function ($query) {
                $query->where('id', config('lunar.geslib.product_type_id'));
            })->with([
                'variant',
                'variant.prices',
                'variant.prices.priceable',
                'variant.prices.priceable.taxClass',
                'variant.prices.priceable.taxClass.taxRateAmounts',
                'variant.prices.currency',
                'media',
                'defaultUrl',
                'authors',
            ])->get();
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.components.news.article-products');
    }
}
