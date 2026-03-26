<?php

namespace Testa\Storefront\Livewire\News;

use Illuminate\View\View;
use Livewire\Attributes\Url;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Livewire\Features\WithPagination;
use Testa\Storefront\Queries\News\GetPublishedArticles;

class ArticlesListPage extends Page
{
    use WithPagination;

    #[Url]
    public string $q = '';

    public function render(): View
    {
        $articles = new GetPublishedArticles()->execute();

        return view('testa::storefront.livewire.news.articles-list', compact('articles'))
            ->title(__('Noticias'));
    }

    public function search(): void
    {
        $this->resetPage();
    }
}
