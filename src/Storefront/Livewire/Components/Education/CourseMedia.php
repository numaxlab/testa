<?php

namespace Testa\Storefront\Livewire\Components\Education;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;
use Testa\Models\Education\Course;
use Testa\Storefront\Queries\Education\GetCourseAttachments;

class CourseMedia extends Component
{
    public Course $course;

    public Collection $attachments;

    public function mount(): void
    {
        $this->attachments = new GetCourseAttachments()
            ->execute($this->course)
            ->filter(fn($attachment) => Gate::allows('view', $attachment->media));
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.education.course-media');
    }
}
