<?php

namespace Testa\Storefront\Livewire\Components\Education;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Testa\Models\Education\Course;
use Testa\Storefront\Queries\Education\GetCourseProducts;

class CourseProducts extends Component
{
    public Course $course;

    public Collection $products;

    public function mount(): void
    {
        $this->products = new GetCourseProducts()->execute($this->course);
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.education.course-products');
    }
}
