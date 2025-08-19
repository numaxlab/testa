<?php

namespace Trafikrak\Admin\Filament\Resources\Media\DocumentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;
use Lunar\Admin\Support\Pages\BaseListRecords;
use Trafikrak\Admin\Filament\Resources\Media\DocumentResource;

class ListDocuments extends BaseListRecords
{
    use Translatable;

    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
