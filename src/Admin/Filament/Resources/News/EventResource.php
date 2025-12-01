<?php

namespace Trafikrak\Admin\Filament\Resources\News;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Lunar\Admin\Support\Resources\BaseResource;
use Trafikrak\Models\EventDeliveryMethod;
use Trafikrak\Models\News\Event;

class EventResource extends BaseResource
{
    use Translatable;

    protected static ?string $model = Event::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public static function getNavigationGroup(): ?string
    {
        return __('trafikrak::global.sections.news');
    }

    public static function getLabel(): string
    {
        return __('trafikrak::event.label');
    }

    public static function getPluralLabel(): string
    {
        return __('trafikrak::event.plural_label');
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-calendar';
    }

    public static function getDefaultSubNavigation(): array
    {
        return [
            EventResource\Pages\EditEvent::class,
            EventResource\Pages\ManageEventUrls::class,
            EventResource\Pages\ManageEventSpeakers::class,
            EventResource\Pages\ManageEventAttachments::class,
            EventResource\Pages\ManageEventProducts::class,
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
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label(__('trafikrak::event.table.is_published.label')),
            ]);
    }

    public static function getDefaultForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('event_type_id')
                            ->relationship('eventType', 'name')
                            ->required()
                            ->label(__('trafikrak::event.form.event_type_id.label')),
                        Forms\Components\TextInput::make('name')
                            ->label(__('trafikrak::event.form.name.label'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        Forms\Components\TextInput::make('subtitle')
                            ->label(__('trafikrak::event.form.subtitle.label'))
                            ->maxLength(255),
                        TiptapEditor::make('description')
                            ->label(__('trafikrak::event.form.description.label'))
                            ->profile('default'),
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label(__('trafikrak::event.form.starts_at.label'))
                            ->required(),
                        Forms\Components\Select::make('delivery_method')
                            ->label(__('trafikrak::event.form.delivery_method.label'))
                            ->required()
                            ->options([
                                EventDeliveryMethod::IN_PERSON->value => __(
                                    'trafikrak::coursemodule.form.delivery_method.options.in_person',
                                ),
                                EventDeliveryMethod::ONLINE->value => __(
                                    'trafikrak::coursemodule.form.delivery_method.options.online',
                                ),
                                EventDeliveryMethod::HYBRID->value => __(
                                    'trafikrak::coursemodule.form.delivery_method.options.hybrid',
                                ),
                            ]),
                        Forms\Components\Select::make('venue_id')
                            ->relationship('venue', 'name')
                            ->required()
                            ->label(__('trafikrak::event.form.venue_id.label')),
                        Forms\Components\Textarea::make('alert')
                            ->label(__('trafikrak::event.form.alert.label')),
                        Forms\Components\TextInput::make('register_url')
                            ->label(__('trafikrak::event.form.register_url.label'))
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('image')
                            ->label(__('trafikrak::event.form.image.label'))
                            ->image()
                            ->imageEditor(),
                        Forms\Components\Toggle::make('is_published')
                            ->label(__('trafikrak::event.form.is_published.label')),
                    ]),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => EventResource\Pages\ListEvents::route('/'),
            'create' => EventResource\Pages\CreateEvent::route('/create'),
            'edit' => EventResource\Pages\EditEvent::route('/{record}/edit'),
            'urls' => EventResource\Pages\ManageEventUrls::route('/{record}/urls'),
            'speakers' => EventResource\Pages\ManageEventSpeakers::route('/{record}/speakers'),
            'attachments' => EventResource\Pages\ManageEventAttachments::route('/{record}/attachments'),
            'products' => EventResource\Pages\ManageEventProducts::route('/{record}/products'),
        ];
    }
}
