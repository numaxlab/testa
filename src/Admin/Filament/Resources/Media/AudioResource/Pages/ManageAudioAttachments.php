<?php

namespace Trafikrak\Admin\Filament\Resources\Media\AudioResource\Pages;

use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Table;
use Lunar\Admin\Support\Pages\BaseManageRelatedRecords;
use Trafikrak\Admin\Filament\Resources\Media\AudioResource;

class ManageAudioAttachments extends BaseManageRelatedRecords
{
    protected static string $resource = AudioResource::class;

    protected static string $relationship = 'attachments';

    public static function getNavigationIcon(): ?string
    {
        return FilamentIcon::resolve('lunar::tags');
    }

    public static function getNavigationLabel(): string
    {
        return __('trafikrak::audio.pages.attachments.label');
    }

    public function getTitle(): string
    {
        return __('trafikrak::audio.pages.attachments.label');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([])
            ->headerActions([])
            ->actions([]);
    }
}
