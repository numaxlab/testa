<?php

namespace Trafikrak\Storefront\Livewire\News;

use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Models\News\Article;

class ArticlesListPage extends Page
{
    use WithPagination;

    #[Url]
    public string $q = '';

    public function render(): View
    {
        $articles = Article::where('is_published', true)
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->with(['defaultUrl'])
            ->paginate(12);

        return view('trafikrak::storefront.livewire.news.articles-list', compact('articles'))
            ->title(__('Noticias'));
    }

    public function search(): void
    {
        $this->resetPage();
    }
}
