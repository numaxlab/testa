<?php

namespace Testa\Admin\Filament\Resources\Content;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Lunar\Admin\Support\Resources\BaseResource;
use Lunar\Models\Collection;
use NumaxLab\Lunar\Geslib\Handle;
use Testa\Models\Content\Section;
use Testa\Models\Content\Tier;
use Testa\Models\Content\TierType;
use Testa\Models\Media\Audio;
use Testa\Models\Media\Video;

class TierResource extends BaseResource
{
    use Translatable;

    protected static ?string $model = Tier::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return __('testa::global.sections.content');
    }

    public static function getLabel(): string
    {
        return __('testa::tier.label');
    }

    public static function getPluralLabel(): string
    {
        return __('testa::tier.plural_label');
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-adjustments-horizontal';
    }

    public static function getDefaultTable(Table $table): Table
    {
        return $table
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('testa::tier.table.name.label'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('testa::tier.table.type.label'))
                    ->formatStateUsing(fn (TierType $state): string => match ($state) {
                        TierType::RELATED_CONTENT_BANNER => __('testa::tier.form.type.options.'.TierType::RELATED_CONTENT_BANNER->value),
                        TierType::RELATED_CONTENT_COLLECTION => __('testa::tier.form.type.options.'.TierType::RELATED_CONTENT_COLLECTION->value),
                        TierType::RELATED_CONTENT_COURSE => __('testa::tier.form.type.options.'.TierType::RELATED_CONTENT_COURSE->value),
                        TierType::RELATED_CONTENT_EDUCATION_TOPIC => __('testa::tier.form.type.options.'.TierType::RELATED_CONTENT_EDUCATION_TOPIC->value),
                        TierType::RELATED_CONTENT_MEDIA => __('testa::tier.form.type.options.'.TierType::RELATED_CONTENT_MEDIA->value),
                        TierType::EDITORIAL_LATEST => __('testa::tier.form.type.options.'.TierType::EDITORIAL_LATEST->value),
                        TierType::EDUCATION_UPCOMING => __('testa::tier.form.type.options.'.TierType::EDUCATION_UPCOMING->value),
                        TierType::EVENTS_UPCOMING => __('testa::tier.form.type.options.'.TierType::EVENTS_UPCOMING->value),
                        TierType::ARTICLES_LATEST => __('testa::tier.form.type.options.'.TierType::ARTICLES_LATEST->value),
                        default => $state->value,
                    })
                    ->badge(),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label(__('testa::tier.table.is_published.label')),
            ])
            ->defaultSort('sort_position')
            ->reorderable('sort_position');
    }

    public static function getDefaultForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('section')
                            ->label(__('testa::tier.form.section.label'))
                            ->required()
                            ->options([
                                Section::HOMEPAGE->value => __('testa::tier.sections.homepage'),
                                Section::BOOKSHOP->value => __('testa::tier.sections.bookshop'),
                                Section::EDITORIAL->value => __('testa::tier.sections.editorial'),
                                Section::EDUCATION->value => __('testa::tier.sections.education'),
                                Section::MEDIA->value => __('testa::tier.sections.media'),
                            ]),
                        Forms\Components\TextInput::make('name')
                            ->label(__('testa::tier.form.name.label'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        Forms\Components\Select::make('type')
                            ->label(__('testa::tier.form.type.label'))
                            ->options([
                                TierType::RELATED_CONTENT_BANNER->value => __('testa::tier.form.type.options.'.TierType::RELATED_CONTENT_BANNER->value),
                                TierType::RELATED_CONTENT_COLLECTION->value => __('testa::tier.form.type.options.'.TierType::RELATED_CONTENT_COLLECTION->value),
                                TierType::RELATED_CONTENT_COURSE->value => __('testa::tier.form.type.options.'.TierType::RELATED_CONTENT_COURSE->value),
                                TierType::RELATED_CONTENT_EDUCATION_TOPIC->value => __('testa::tier.form.type.options.'.TierType::RELATED_CONTENT_EDUCATION_TOPIC->value),
                                TierType::RELATED_CONTENT_MEDIA->value => __('testa::tier.form.type.options.'.TierType::RELATED_CONTENT_MEDIA->value),
                                TierType::EDITORIAL_LATEST->value => __('testa::tier.form.type.options.'.TierType::EDITORIAL_LATEST->value),
                                TierType::EDUCATION_UPCOMING->value => __('testa::tier.form.type.options.'.TierType::EDUCATION_UPCOMING->value),
                                TierType::EVENTS_UPCOMING->value => __('testa::tier.form.type.options.'.TierType::EVENTS_UPCOMING->value),
                                TierType::ARTICLES_LATEST->value => __('testa::tier.form.type.options.'.TierType::ARTICLES_LATEST->value),
                            ])
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('banners')
                            ->relationship(titleAttribute: 'name')
                            ->label(__('testa::tier.form.banners.label'))
                            ->multiple()
                            ->preload()
                            ->visible(fn (Get $get) => $get('type') === TierType::RELATED_CONTENT_BANNER->value),
                        Forms\Components\Select::make('collections')
                            ->relationship(
                                modifyQueryUsing: fn (Builder $query)
                                    => $query->whereHas(
                                    'group',
                                    function (Builder $query) {
                                        $query
                                            ->whereIn('handle', [
                                                Handle::COLLECTION_GROUP_TAXONOMIES,
                                                Handle::COLLECTION_GROUP_FEATURED,
                                                \Testa\Handle::COLLECTION_GROUP_EDITORIAL_FEATURED,
                                                Handle::COLLECTION_GROUP_ITINERARIES,
                                            ]);
                                    },
                                ),
                            )
                            ->getOptionLabelFromRecordUsing(function (Collection $record): string {
                                return "{$record->translateAttribute('name')} [{$record->group->name}]";
                            })
                            ->label(__('testa::tier.form.collections.label'))
                            ->multiple()
                            ->preload()
                            ->hint('taxonomías, destacados, destacados editorial, itinerarios')
                            ->visible(fn (Get $get) => $get('type') === TierType::RELATED_CONTENT_COLLECTION->value),
                        Forms\Components\Select::make('courses')
                            ->relationship(titleAttribute: 'name')
                            ->label(__('testa::tier.form.courses.label'))
                            ->multiple()
                            ->preload()
                            ->visible(fn (Get $get) => $get('type') === TierType::RELATED_CONTENT_COURSE->value),
                        Forms\Components\Select::make('educationTopics')
                            ->relationship(titleAttribute: 'name')
                            ->label(__('testa::tier.form.education_topics.label'))
                            ->multiple()
                            ->preload()
                            ->visible(fn (Get $get)
                                => $get('type') === TierType::RELATED_CONTENT_EDUCATION_TOPIC->value),
                        Forms\Components\Repeater::make('attachments')
                            ->relationship()
                            ->label(__('testa::tier.form.attachments.label'))
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->columns([
                                        'sm' => 1,
                                        'md' => 2,
                                    ])
                                    ->schema([
                                        Forms\Components\Select::make('media_type')
                                            ->label(__('testa::tier.form.attachments.media_type'))
                                            ->options([
                                                (new Video)->getMorphClass() => 'Vídeo',
                                                (new Audio)->getMorphClass() => 'Audio',
                                            ])
                                            ->reactive()
                                            ->required(),
                                        Forms\Components\Select::make('media_id')
                                            ->label(__('testa::tier.form.attachments.media_id'))
                                            ->options(function (callable $get) {
                                                $type = $get('media_type');
                                                if (! $type) {
                                                    return [];
                                                }

                                                return match ($type) {
                                                    (new Video)->getMorphClass() => Video::all()->pluck('name', 'id'),
                                                    (new Audio)->getMorphClass() => Audio::all()->pluck('name', 'id'),
                                                    default => [],
                                                };
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                    ]),
                            ])
                            ->defaultItems(0)
                            ->minItems(0)
                            ->visible(fn (Get $get) => $get('type') === TierType::RELATED_CONTENT_MEDIA->value),
                        Forms\Components\Grid::make()
                            ->columns([
                                'sm' => 1,
                                'md' => 2,
                            ])
                            ->schema([
                                Forms\Components\TextInput::make('link')
                                    ->label(__('testa::tier.form.link.label')),
                                Forms\Components\TextInput::make('link_name')
                                    ->label(__('testa::tier.form.link_name.label'))
                                    ->maxLength(255),
                            ])
                            ->visible(fn (Get $get) => $get('type') !== TierType::RELATED_CONTENT_BANNER->value),
                        Forms\Components\Toggle::make('is_published')
                            ->label(__('testa::tier.form.is_published.label')),
                    ]),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => TierResource\Pages\ListTiers::route('/'),
            'create' => TierResource\Pages\CreateTier::route('/create'),
            'edit' => TierResource\Pages\EditTier::route('/{record}/edit'),
        ];
    }

    protected static function getDefaultRelations(): array
    {
        return [];
    }
}
