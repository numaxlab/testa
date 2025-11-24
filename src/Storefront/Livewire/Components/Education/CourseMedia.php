<?php

namespace Trafikrak\Storefront\Livewire\Components\Education;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Trafikrak\Models\Attachment;
use Trafikrak\Models\Education\Course;
use Trafikrak\Models\Education\CourseModule;

class CourseMedia extends Component
{
    public Course $course;

    public Collection $attachments;

    public function mount(): void
    {
        $this->attachments = Attachment::where(function ($query) {
            $query->where(function ($query) {
                $query
                    ->where('attachable_type', (new Course)->getMorphClass())
                    ->where('attachable_id', $this->course->id);
            })->orWhere(function ($query) {
                $query
                    ->where('attachable_type', (new CourseModule)->getMorphClass())
                    ->whereIn('attachable_id', $this->course->modules->pluck('id'));
            });
        })->whereHas('media', fn ($query) => $query->where('is_published', true))
            ->with('media')
            ->get();
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.components.education.course-media');
    }
}
