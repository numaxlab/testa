<?php

namespace Testa\Storefront\Livewire\Components\Tier;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Testa\Models\Content\Tier;
use Testa\Storefront\Queries\News\GetLatestArticles;

class ArticlesLatest extends Component
{
    public Tier $tier;

    public Collection $articles;

    public function mount(): void
    {
        $this->articles = new GetLatestArticles()->execute();
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.tier.articles-latest');
    }
}
