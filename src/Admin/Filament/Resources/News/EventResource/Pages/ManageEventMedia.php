<?php

namespace Testa\Admin\Filament\Resources\News\EventResource\Pages;

use Lunar\Admin\Support\Resources\Pages\ManageMediasRelatedRecords;
use Testa\Admin\Filament\Resources\News\EventResource;

class ManageEventMedia extends ManageMediasRelatedRecords
{
    protected static string $resource = EventResource::class;
}
