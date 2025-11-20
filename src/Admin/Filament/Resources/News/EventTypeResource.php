<?php

namespace Trafikrak\Admin\Filament\Resources\News;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;
use Lunar\Admin\Support\Resources\BaseResource;
use Trafikrak\Models\News\EventType;

class EventTypeResource extends BaseResource
{
    use Translatable;

    protected static ?string $model = EventType::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public static function getNavigationGroup(): ?string
    {
        return __('trafikrak::global.sections.news');
    }

    public static function getLabel(): string
    {
        return __('trafikrak::event-type.label');
    }

    public static function getPluralLabel(): string
    {
        return __('trafikrak::event-type.plural_label');
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-list-bullet';
    }

    public static function getDefaultSubNavigation(): array
    {
        return [
            EventTypeResource\Pages\EditEventType::class,
        ];
    }

    public static function getDefaultTable(Table $table): Table
    {
        return $table
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('trafikrak::event.table.name.label'))
                    ->searchable(),
            ]);
    }

    public static function getDefaultForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('trafikrak::event.form.name.label'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                    ]),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => EventTypeResource\Pages\ListEventTypes::route('/'),
            'create' => EventTypeResource\Pages\CreateEventType::route('/create'),
            'edit' => EventTypeResource\Pages\EditEventType::route('/{record}/edit'),
        ];
    }
}
