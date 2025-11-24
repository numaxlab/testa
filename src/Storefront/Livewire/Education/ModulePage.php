<?php

namespace Trafikrak\Storefront\Livewire\Education;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Models\Education\CourseModule;

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

            if ($customer->courses->contains($this->module->course)) {
                $userRegistered = true;
            }
        }

        return view('trafikrak::storefront.livewire.education.module', compact('userRegistered'));
    }
}
