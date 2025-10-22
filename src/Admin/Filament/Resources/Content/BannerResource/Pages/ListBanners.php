<?php

namespace Trafikrak\Admin\Filament\Resources\Content\BannerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;
use Lunar\Admin\Support\Pages\BaseListRecords;
use Trafikrak\Admin\Filament\Resources\Content\BannerResource;

class ListBanners extends BaseListRecords
{
    use Translatable;

    protected static string $resource = BannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
