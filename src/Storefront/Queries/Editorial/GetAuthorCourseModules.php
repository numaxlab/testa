<?php

namespace Testa\Storefront\Queries\Editorial;

use Illuminate\Database\Eloquent\Collection;
use NumaxLab\Lunar\Geslib\Models\Author;
use Testa\Models\Education\CourseModule;

final class GetAuthorCourseModules
{
    public function execute(Author $author): Collection
    {
        return CourseModule::whereHas('instructors', function ($query) use ($author) {
            $query->where((new Author)->getTable().'.id', $author->id);
        })->where('is_published', true)->get();
    }
}
