<?php

namespace Testa\Storefront\Queries\Education;

use Illuminate\Database\Eloquent\Collection;
use Testa\Models\Education\Course;
use Testa\Models\Education\CourseModule;

final class GetCourseModules
{
    public function execute(Course $course, ?CourseModule $except = null): Collection
    {
        return CourseModule::where('course_id', $course->id)
            ->where('is_published', true)
            ->when($except, fn($query) => $query->where('id', '!=', $except->id))
            ->orderBy('starts_at')
            ->with(['defaultUrl', 'course', 'course.defaultUrl'])
            ->get();
    }
}
