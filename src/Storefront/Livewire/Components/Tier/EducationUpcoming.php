<?php

namespace Testa\Storefront\Livewire\Components\Tier;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Testa\Models\Content\Tier;
use Testa\Storefront\Queries\Education\GetUpcomingCourses;

class EducationUpcoming extends Component
{
    public Tier $tier;

    public Collection $courses;

    public function mount(): void
    {
        $this->courses = new GetUpcomingCourses()->execute();
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.tier.courses');
    }
}
