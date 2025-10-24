<?php

namespace Trafikrak\Storefront\Livewire\Components\Tier;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Trafikrak\Models\Content\Tier;
use Trafikrak\Models\Education\Course;

class EducationUpcoming extends Component
{
    public Tier $tier;

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
            ->limit(4)
            ->get();
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.components.tier.courses');
    }
}
