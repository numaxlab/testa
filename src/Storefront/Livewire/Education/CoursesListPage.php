<?php

namespace Trafikrak\Storefront\Livewire\Education;

use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Models\Education\Course;
use Trafikrak\Models\Education\Topic;

class CoursesListPage extends Page
{
    use WithPagination;

    #[Url]
    public string $q = '';

    #[Url]
    public string $t = '';

    public function render(): View
    {
        $topics = Topic::where('is_published', true)
            ->with([
                'media',
                'defaultUrl',
            ])
            ->get();

        $queryBuilder = Course::where('is_published', true)
            ->with([
                'media',
                'defaultUrl',
                'topic',
            ])
            ->orderBy('ends_at', 'desc');

        if ($this->q) {
            $coursesByQuery = Course::search($this->q)->get();

            $queryBuilder->whereIn('id', $coursesByQuery->pluck('id'));
        }

        if ($this->t) {
            $queryBuilder->whereHas('topic', function ($query) {
                $query->where('id', $this->t);
            });
        }

        $courses = $queryBuilder->paginate(12);

        return view('trafikrak::storefront.livewire.education.courses-list', compact('topics', 'courses'))
            ->title(__('Cursos'));
    }

    public function search(): void
    {
        $this->resetPage();
    }
}
