<?php

namespace Trafikrak\Admin\Filament\Resources\Content\TierResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;
use Lunar\Admin\Support\Pages\BaseEditRecord;
use Trafikrak\Admin\Filament\Resources\Content\TierResource;

class EditTier extends BaseEditRecord
{
    use Translatable;

    protected static string $resource = TierResource::class;

    public static function getNavigationLabel(): string
    {
        return __('trafikrak::membership-plan.pages.edit.title');
    }

    public function getTitle(): string
    {
        return __('trafikrak::membership-plan.pages.edit.title');
    }

    protected function getDefaultHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
