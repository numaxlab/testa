<?php

namespace Testa\Storefront\Livewire\Education;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Models\Content\Banner;
use Testa\Models\Content\Location;
use Testa\Models\Education\Course;

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
                route('testa.storefront.education.courses.register', $this->course->defaultUrl->slug)
                : null;
        }

        $userRegistered = false;

        if (Auth::check()) {
            $customer = Auth::user()->latestCustomer();

            if ($customer->courses->contains($this->course)) {
                $userRegistered = true;
            }
        }

        $media = $this->course->verticalImage;

        return view('testa::storefront.livewire.education.course', compact('banner', 'userRegistered', 'media'))
            ->title($this->course->fullTitle);
    }
}
