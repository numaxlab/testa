<?php

namespace Trafikrak\Admin\Filament\Resources\Education\TopicResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;
use Lunar\Admin\Support\Pages\BaseCreateRecord;
use Trafikrak\Admin\Filament\Resources\Education\TopicResource;

class CreateTopic extends BaseCreateRecord
{
    use Translatable;

    protected static string $resource = TopicResource::class;

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
