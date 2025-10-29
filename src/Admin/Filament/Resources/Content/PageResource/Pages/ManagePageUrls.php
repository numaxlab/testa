<?php

namespace Trafikrak\Admin\Filament\Resources\Content\PageResource\Pages;

use Lunar\Admin\Support\Resources\Pages\ManageUrlsRelatedRecords;
use Trafikrak\Admin\Filament\Resources\Content\PageResource;
use Trafikrak\Models\Content\Page;

class ManagePageUrls extends ManageUrlsRelatedRecords
{
    protected static string $resource = PageResource::class;

    protected static string $model = Page::class;
}
