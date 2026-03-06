<?php

namespace Testa\Storefront\Livewire\News;

use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Models\News\Article;

class ArticlePage extends Page
{
    public Article $article;

    public function mount($slug): void
    {
        $this->fetchUrl(
            slug: $slug,
            type: (new Article)->getMorphClass(),
            firstOrFail: true,
            eagerLoad: [
                'element.media',
            ],
        );

        $this->article = $this->url->element;
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.news.article')
            ->title($this->article->name);
    }
}
