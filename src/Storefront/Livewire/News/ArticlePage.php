<?php

namespace Trafikrak\Storefront\Livewire\News;

use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Models\News\Article;

class ArticlePage extends Page
{
    public Article $article;

    public function mount($slug): void
    {
        $this->fetchUrl(
            slug: $slug,
            type: (new Article)->getMorphClass(),
            firstOrFail: true,
        );

        $this->article = $this->url->element;
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.news.article')
            ->title($this->article->name);
    }
}
