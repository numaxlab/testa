<?php

namespace Trafikrak\Storefront\Livewire\Components\Education;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Trafikrak\Models\Education\Course;

class CurrentCourses extends Component
{
    public Collection $courses;

    public function mount(): void
    {
        $this->courses = Course::where('is_published', true)
            ->where('ends_at', '>=', now())
            ->with([
                'media',
                'defaultUrl',
                'topic',
            ])
            ->orderBy('starts_at', 'asc')
            ->limit(6)
            ->get();
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.components.education.current-courses');
    }
}
