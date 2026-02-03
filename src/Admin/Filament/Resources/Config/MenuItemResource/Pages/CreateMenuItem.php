<?php

namespace Testa\Admin\Filament\Resources\Config\MenuItemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;
use Lunar\Admin\Support\Pages\BaseCreateRecord;
use Testa\Admin\Filament\Resources\Config\MenuItemResource;

class CreateMenuItem extends BaseCreateRecord
{
    use Translatable;

    protected static string $resource = MenuItemResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
}
