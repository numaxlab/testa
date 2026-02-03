<?php

namespace Testa\Admin\Filament\Resources\Config\MenuItemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;
use Lunar\Admin\Support\Pages\BaseListRecords;
use Testa\Admin\Filament\Resources\Config\MenuItemResource;

class ListMenuItems extends BaseListRecords
{
    use Translatable;

    protected static string $resource = MenuItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
