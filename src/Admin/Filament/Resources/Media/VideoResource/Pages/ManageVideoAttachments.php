<?php

namespace Testa\Admin\Filament\Resources\Media\VideoResource\Pages;

use Testa\Admin\Filament\Resources\Media\VideoResource;
use Testa\Admin\Filament\Support\Page\ManageMediaUsagesRelatedRecords;

class ManageVideoAttachments extends ManageMediaUsagesRelatedRecords
{
    protected static string $resource = VideoResource::class;

    protected static string $relationship = 'attachments';

    public static function getNavigationLabel(): string
    {
        return __('testa::video.pages.attachments.label');
    }

    public function getTitle(): string
    {
        return __('testa::video.pages.attachments.label');
    }
}
