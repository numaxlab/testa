<?php

namespace Trafikrak\Admin\Filament\Resources\Media\VideoResource\Pages;

use Lunar\Admin\Support\Resources\Pages\ManageUrlsRelatedRecords;
use Trafikrak\Admin\Filament\Resources\Media\VideoResource;
use Trafikrak\Models\Media\Video;

class ManageVideoUrls extends ManageUrlsRelatedRecords
{
    protected static string $resource = VideoResource::class;

    protected static string $model = Video::class;
}
