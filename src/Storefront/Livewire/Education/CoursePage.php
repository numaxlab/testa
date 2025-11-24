<?php

namespace Trafikrak\Storefront\Livewire\Education;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Models\Content\Banner;
use Trafikrak\Models\Content\Location;
use Trafikrak\Models\Education\Course;

class CoursePage extends Page
{
    public Course $course;

    public function mount($slug): void
    {
        $this->fetchUrl(
            slug: $slug,
            type: (new Course)->getMorphClass(),
            firstOrFail: true,
            eagerLoad: [
                'element.topic',
                'element.media',
                'element.purchasable',
            ],
        );

        $this->course = $this->url->element;
    }

    public function render(): View
    {
        $banner = Banner::whereJsonContains('locations', Location::COURSE->value)
            ->where('is_published', true)
            ->first();

        if ($banner) {
            $banner->link = $this->course->purchasable ?
                route('trafikrak.storefront.education.courses.register', $this->course->defaultUrl->slug)
                : null;
        }

        $userRegistered = false;

        if (Auth::check()) {
            $customer = Auth::user()->latestCustomer();

            if ($customer->courses->contains($this->course)) {
                $userRegistered = true;
            }
        }

        return view('trafikrak::storefront.livewire.education.course', compact('banner', 'userRegistered'));
    }
}
