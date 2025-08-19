<?php

namespace Trafikrak\Admin\Filament\Resources\Media\AudioResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;
use Lunar\Admin\Support\Pages\BaseEditRecord;
use Trafikrak\Admin\Filament\Resources\Media\AudioResource;

class EditAudio extends BaseEditRecord
{
    use Translatable;

    protected static string $resource = AudioResource::class;

    public static function getNavigationLabel(): string
    {
        return __('trafikrak::audio.pages.edit.title');
    }

    public function getTitle(): string
    {
        return __('trafikrak::audio.pages.edit.title');
    }

    protected function getDefaultHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
