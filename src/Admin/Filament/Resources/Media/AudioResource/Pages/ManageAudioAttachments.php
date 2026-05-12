<?php

namespace Testa\Admin\Filament\Resources\Media\AudioResource\Pages;

use Testa\Admin\Filament\Resources\Media\AudioResource;
use Testa\Admin\Filament\Support\Page\ManageMediaUsagesRelatedRecords;

class ManageAudioAttachments extends ManageMediaUsagesRelatedRecords
{
    protected static string $resource = AudioResource::class;

    protected static string $relationship = 'attachments';

    public static function getNavigationLabel(): string
    {
        return __('testa::audio.pages.attachments.label');
    }

    public function getTitle(): string
    {
        return __('testa::audio.pages.attachments.label');
    }
}
