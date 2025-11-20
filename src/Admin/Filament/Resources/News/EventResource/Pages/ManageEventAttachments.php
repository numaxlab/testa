<?php

namespace Trafikrak\Admin\Filament\Resources\News\EventResource\Pages;

use Trafikrak\Admin\Filament\Resources\News\EventResource;
use Trafikrak\Admin\Filament\Support\Page\ManageAttachmentsRelatedRecords;

class ManageEventAttachments extends ManageAttachmentsRelatedRecords
{
    protected static string $resource = EventResource::class;

    protected static string $relationship = 'attachments';
}
