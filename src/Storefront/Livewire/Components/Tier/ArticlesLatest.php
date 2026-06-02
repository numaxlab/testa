<?php

namespace Testa\Storefront\Livewire\Components\Tier;

use Illuminate\View\View;
use Livewire\Component;
use Testa\Models\Content\Tier;
use Testa\Storefront\Queries\News\GetLatestArticles;

class ArticlesLatest extends Component
{
    public Tier $tier;

    public function render(): View
    {
        $articles = new GetLatestArticles()->execute();

        return view('testa::storefront.livewire.components.tier.articles-latest', compact('articles'));
    }
}
