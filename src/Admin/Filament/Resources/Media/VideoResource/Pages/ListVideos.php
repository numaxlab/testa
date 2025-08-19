<?php

namespace Trafikrak\Admin\Filament\Resources\Media\VideoResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;
use Lunar\Admin\Support\Pages\BaseListRecords;
use Trafikrak\Admin\Filament\Resources\Media\VideoResource;

class ListVideos extends BaseListRecords
{
    use Translatable;

    protected static string $resource = VideoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
