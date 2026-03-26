<?php

namespace Testa\Storefront\Queries\Education;

use NumaxLab\Lunar\Geslib\Models\Author;
use Testa\Models\Education\CourseModule;

final class CheckAuthorHasMedia
{
    public function execute(Author $author): bool
    {
        return CourseModule::whereHas('instructors', function ($query) use ($author) {
            $query->where((new Author)->getTable().'.id', $author->id);
        })->where('is_published', true)->exists();
    }
}
