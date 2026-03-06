<?php

namespace Testa\Admin\Filament\Resources\News;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Lunar\Admin\Support\Resources\BaseResource;
use Testa\Models\EventDeliveryMethod;
use Testa\Models\News\Event;

class EventResource extends BaseResource
{
    use Translatable;

    protected static ?string $model = Event::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public static function getNavigationGroup(): ?string
    {
        return __('testa::global.sections.news');
    }

    public static function getLabel(): string
    {
        return __('testa::event.label');
    }

    public static function getPluralLabel(): string
    {
        return __('testa::event.plural_label');
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-calendar';
    }

    public static function getDefaultSubNavigation(): array
    {
        return [
            EventResource\Pages\EditEvent::class,
            EventResource\Pages\ManageEventMedia::class,
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
                SpatieMediaLibraryImageColumn::make('thumbnail')
                    ->collection(config('lunar.media.collection'))
                    ->conversion('small')
                    ->limit(1)
                    ->square()
                    ->label(''),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('testa::event.table.name.label'))
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label(__('testa::event.table.is_published.label')),
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
                            ->label(__('testa::event.form.event_type_id.label')),
                        Forms\Components\TextInput::make('name')
                            ->label(__('testa::event.form.name.label'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        Forms\Components\TextInput::make('subtitle')
                            ->label(__('testa::event.form.subtitle.label'))
                            ->maxLength(255),
                        TiptapEditor::make('description')
                            ->label(__('testa::event.form.description.label'))
                            ->profile('default'),
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label(__('testa::event.form.starts_at.label'))
                            ->required(),
                        Forms\Components\Select::make('delivery_method')
                            ->label(__('testa::event.form.delivery_method.label'))
                            ->required()
                            ->options([
                                EventDeliveryMethod::IN_PERSON->value => __(
                                    'testa::coursemodule.form.delivery_method.options.in_person',
                                ),
                                EventDeliveryMethod::ONLINE->value => __(
                                    'testa::coursemodule.form.delivery_method.options.online',
                                ),
                                EventDeliveryMethod::HYBRID->value => __(
                                    'testa::coursemodule.form.delivery_method.options.hybrid',
                                ),
                            ]),
                        Forms\Components\Select::make('venue_id')
                            ->relationship('venue', 'name')
                            ->required()
                            ->label(__('testa::event.form.venue_id.label')),
                        Forms\Components\Textarea::make('alert')
                            ->label(__('testa::event.form.alert.label')),
                        Forms\Components\TextInput::make('organiser')
                            ->label(__('testa::event.form.organiser.label'))
                            ->maxLength(255),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('register_url')
                                    ->label(__('testa::event.form.register_url.label'))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('register_tag')
                                    ->label(__('testa::event.form.register_tag.label'))
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Toggle::make('is_published')
                            ->label(__('testa::event.form.is_published.label')),
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
            'media' => EventResource\Pages\ManageEventMedia::route('/{record}/media'),
            'products' => EventResource\Pages\ManageEventProducts::route('/{record}/products'),
        ];
    }
}
