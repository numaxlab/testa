<?php

namespace Trafikrak\Admin\Filament\Resources\Education\TopicResource\Pages;

use Lunar\Admin\Support\Resources\Pages\ManageUrlsRelatedRecords;
use Trafikrak\Admin\Filament\Resources\Education\TopicResource;
use Trafikrak\Models\Education\Topic;

class ManageTopicUrls extends ManageUrlsRelatedRecords
{
    protected static string $resource = TopicResource::class;

    protected static string $model = Topic::class;
}
