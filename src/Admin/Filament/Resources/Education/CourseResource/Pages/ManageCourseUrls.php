<?php

namespace Trafikrak\Admin\Filament\Resources\Education\CourseResource\Pages;

use Lunar\Admin\Support\Resources\Pages\ManageUrlsRelatedRecords;
use Trafikrak\Admin\Filament\Resources\Education\CourseResource;
use Trafikrak\Models\Education\Course;

class ManageCourseUrls extends ManageUrlsRelatedRecords
{
    protected static string $resource = CourseResource::class;

    protected static string $model = Course::class;
}
