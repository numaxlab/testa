<?php

namespace Trafikrak\Admin\Filament\Resources\News\EventResource\Pages;

use Lunar\Admin\Support\Resources\Pages\ManageUrlsRelatedRecords;
use Trafikrak\Admin\Filament\Resources\News\EventResource;
use Trafikrak\Models\News\Event;

class ManageEventUrls extends ManageUrlsRelatedRecords
{
    protected static string $resource = EventResource::class;

    protected static string $model = Event::class;
}
