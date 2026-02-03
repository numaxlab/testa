<?php

namespace Testa\Admin\Filament\Resources\Config\MenuItemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;
use Lunar\Admin\Support\Pages\BaseEditRecord;
use Testa\Admin\Filament\Resources\Config\MenuItemResource;

class EditMenuItem extends BaseEditRecord
{
    use Translatable;

    protected static string $resource = MenuItemResource::class;

    public static function getNavigationLabel(): string
    {
        return __('testa::menu-item.pages.edit.title');
    }

    public function getTitle(): string
    {
        return __('testa::menu-item.pages.edit.title');
    }

    protected function getDefaultHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
