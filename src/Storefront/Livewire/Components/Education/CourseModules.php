<?php

namespace Testa\Storefront\Livewire\Components\Education;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Testa\Models\Education\Course;
use Testa\Models\Education\CourseModule;
use Testa\Storefront\Queries\Education\GetCourseModules;

class CourseModules extends Component
{
    public Course $course;

    public ?CourseModule $except = null;

    public string $title;
    public Collection $modules;

    public function mount(): void
    {
        $this->modules = new GetCourseModules()->execute($this->course, $this->except);
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.education.course-modules');
    }
}
