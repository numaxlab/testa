<?php

namespace Testa\Storefront\Livewire\Education;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Models\Education\CourseModule;
use Testa\Storefront\Queries\Education\CheckCustomerCourseEnrolment;

class ModulePage extends Page
{
    public CourseModule $module;

    public function mount($courseSlug, $moduleSlug): void
    {
        $this->fetchUrl(
            slug: $moduleSlug,
            type: (new CourseModule)->getMorphClass(),
            firstOrFail: true,
            eagerLoad: [
                'element.instructors',
                'element.course',
                'element.course.defaultUrl',
            ],
        );

        $this->module = $this->url->element;
    }

    public function render(): View
    {
        $userRegistered = false;

        if (Auth::check()) {
            $customer = Auth::user()->latestCustomer();
            $userRegistered = new CheckCustomerCourseEnrolment()->execute($customer, $this->module->course);
        }

        $media = $this->module->course->verticalImage;

        return view('testa::storefront.livewire.education.module', compact('userRegistered', 'media'));
    }
}
