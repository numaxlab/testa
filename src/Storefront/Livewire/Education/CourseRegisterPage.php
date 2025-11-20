<?php

namespace Trafikrak\Storefront\Livewire\Education;

use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Models\Content\Banner;
use Trafikrak\Models\Content\Location;
use Trafikrak\Models\Education\Course;

class CourseRegisterPage extends Page
{
    public Course $course;

    public array $paymentTypes = [];

    public ?string $selectedVariant;

    public ?string $paymentType = null;

    public function mount($slug): void
    {
        $this->fetchUrl(
            slug: $slug,
            type: (new Course)->getMorphClass(),
            firstOrFail: true,
            eagerLoad: [
                'element.topic',
                'element.purchasable',
                'element.purchasable.variants',
                'element.purchasable.variants.values',
            ],
        );

        $this->course = $this->url->element;

        $this->paymentTypes = config('trafikrak.payment_types.education');
    }

    public function render(): View
    {
        $banner = Banner::whereJsonContains('locations', Location::COURSE_REGISTER->value)
            ->where('is_published', true)
            ->first();

        return view('trafikrak::storefront.livewire.education.course-register', compact('banner'));
    }
}
