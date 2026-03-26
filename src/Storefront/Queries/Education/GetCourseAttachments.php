<?php

namespace Testa\Storefront\Queries\Education;

use Illuminate\Database\Eloquent\Collection;
use Testa\Models\Attachment;
use Testa\Models\Education\Course;
use Testa\Models\Education\CourseModule;

final class GetCourseAttachments
{
    public function execute(Course $course): Collection
    {
        return Attachment::where(function ($query) use ($course) {
            $query->where(function ($query) use ($course) {
                $query
                    ->where('attachable_type', (new Course)->getMorphClass())
                    ->where('attachable_id', $course->id);
            })->orWhere(function ($query) use ($course) {
                $query
                    ->where('attachable_type', (new CourseModule)->getMorphClass())
                    ->whereIn('attachable_id', $course->modules->pluck('id'));
            });
        })->whereHas('media', fn($query) => $query->where('is_published', true))
            ->with('media')
            ->get();
    }
}
