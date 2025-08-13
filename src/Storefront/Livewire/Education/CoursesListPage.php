<?php

namespace Trafikrak\Storefront\Livewire\Education;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Models\Education\Course;

class CoursesListPage extends Page
{
    public Collection $courses;

    public function mount(): void
    {
        $this->courses = Course::where('is_published', true)
            ->with([
                'media',
                'defaultUrl',
                'topic',
            ])
            ->orderBy('ends_at', 'desc')
            ->limit(6)
            ->get();
    }


    public function render(): View
    {
        return view('trafikrak::storefront.livewire.education.courses-list');
    }
}
