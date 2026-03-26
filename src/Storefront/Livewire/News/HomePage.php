<?php

namespace Testa\Storefront\Livewire\News;

use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Storefront\Queries\News\GetLatestActivities;
use Testa\Storefront\Queries\News\GetLatestArticles;

class HomePage extends Page
{
    public function render(): View
    {
        $activities = new GetLatestActivities()->execute();

        $articles = new GetLatestArticles()->execute();

        return view('testa::storefront.livewire.news.homepage', compact('activities', 'articles'))
            ->title(__('Actualidad'));
    }
}
