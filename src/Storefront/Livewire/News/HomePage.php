<?php

namespace Trafikrak\Storefront\Livewire\News;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Models\Education\CourseModule;
use Trafikrak\Models\News\Article;
use Trafikrak\Models\News\Event;

class HomePage extends Page
{
    private array $columns = ['id', 'starts_at'];

    public function render(): View
    {
        $eventsQuery = Event::query()
            ->select([...$this->columns, DB::raw("'event' as type")])
            ->where('is_published', true)
            ->where('starts_at', '>=', now());

        $courseModulesQuery = CourseModule::query()
            ->select([...$this->columns, DB::raw("'course-module' as type")])
            ->where('is_published', true)
            ->where('starts_at', '>=', now());

        $activities = ActivitiesListPage::eagerLoadResults(
            $eventsQuery
                ->union($courseModulesQuery)
                ->orderBy('starts_at', 'asc')
                ->take(4)
                ->get(),
        );

        $articles = Article::where('is_published', true)
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->with(['defaultUrl'])
            ->take(4)
            ->get();

        return view('trafikrak::storefront.livewire.news.homepage', compact('activities', 'articles'))
            ->title(__('Actualidad'));
    }
}
