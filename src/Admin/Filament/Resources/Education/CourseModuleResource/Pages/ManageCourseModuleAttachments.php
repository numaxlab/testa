<?php

namespace Trafikrak\Admin\Filament\Resources\Education\CourseModuleResource\Pages;

use Trafikrak\Admin\Filament\Resources\Education\CourseModuleResource;
use Trafikrak\Admin\Filament\Support\Page\ManageAttachmentsRelatedRecords;

class ManageCourseModuleAttachments extends ManageAttachmentsRelatedRecords
{
    protected static string $resource = CourseModuleResource::class;

    protected static string $relationship = 'attachments';
}
