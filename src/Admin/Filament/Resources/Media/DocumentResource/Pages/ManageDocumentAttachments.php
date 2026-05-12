<?php

namespace Testa\Admin\Filament\Resources\Media\DocumentResource\Pages;

use Testa\Admin\Filament\Resources\Media\DocumentResource;
use Testa\Admin\Filament\Support\Page\ManageMediaUsagesRelatedRecords;

class ManageDocumentAttachments extends ManageMediaUsagesRelatedRecords
{
    protected static string $resource = DocumentResource::class;

    protected static string $relationship = 'attachments';

    public static function getNavigationLabel(): string
    {
        return __('testa::document.pages.attachments.label');
    }

    public function getTitle(): string
    {
        return __('testa::document.pages.attachments.label');
    }
}
