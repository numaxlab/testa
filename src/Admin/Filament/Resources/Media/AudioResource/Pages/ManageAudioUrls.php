<?php

namespace Trafikrak\Admin\Filament\Resources\Media\AudioResource\Pages;

use Lunar\Admin\Support\Resources\Pages\ManageUrlsRelatedRecords;
use Trafikrak\Admin\Filament\Resources\Media\AudioResource;
use Trafikrak\Models\Media\Audio;

class ManageAudioUrls extends ManageUrlsRelatedRecords
{
    protected static string $resource = AudioResource::class;

    protected static string $model = Audio::class;
}
