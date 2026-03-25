<?php

namespace Testa\Storefront\Livewire\Components\Education;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Testa\Models\Education\Course;
use Testa\Storefront\Queries\ProductQueryBuilder;

class CourseProducts extends Component
{
    public Course $course;

    public Collection $products;

    public function mount(): void
    {
        $this->products = ProductQueryBuilder::fromRelation($this->course->products())->get();

        $this->course->modules->each(function ($module) {
            $this->products = $this->products->merge(
                ProductQueryBuilder::fromRelation($module->products())->get(),
            );
        });
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.education.course-products');
    }
}
