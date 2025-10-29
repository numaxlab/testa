<?php

namespace Trafikrak\Admin\Filament\Resources\Content\PageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;
use Lunar\Admin\Support\Pages\BaseEditRecord;
use Trafikrak\Admin\Filament\Resources\Content\PageResource;

class EditPage extends BaseEditRecord
{
    use Translatable;

    protected static string $resource = PageResource::class;

    public static function getNavigationLabel(): string
    {
        return __('trafikrak::page.pages.edit.title');
    }

    public function getTitle(): string
    {
        return __('trafikrak::page.pages.edit.title');
    }

    protected function getDefaultHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
