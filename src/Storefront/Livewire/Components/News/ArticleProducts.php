<?php

namespace Testa\Storefront\Livewire\Components\News;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Testa\Models\News\Article;
use Testa\Storefront\Queries\ProductQueryBuilder;

class ArticleProducts extends Component
{
    public Article $article;

    public Collection $products;

    public function mount(): void
    {
        $this->products = ProductQueryBuilder::fromRelation($this->article->products())->get();
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.news.article-products');
    }
}
