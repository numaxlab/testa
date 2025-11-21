<?php

namespace Trafikrak\Admin\Filament\Resources\Education\VenueResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;
use Lunar\Admin\Support\Pages\BaseCreateRecord;
use Trafikrak\Admin\Filament\Resources\Education\VenueResource;

class CreateVenue extends BaseCreateRecord
{
    use Translatable;

    protected static string $resource = VenueResource::class;

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
