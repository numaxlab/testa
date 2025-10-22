<?php

namespace Trafikrak\Admin\Filament\Resources\Content;

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
use Trafikrak\Models\Content\Section;
use Trafikrak\Models\Content\Tier;
use Trafikrak\Models\Content\TierType;

class TierResource extends BaseResource
{
    use Translatable;

    protected static ?string $model = Tier::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return __('trafikrak::global.sections.content');
    }

    public static function getLabel(): string
    {
        return __('trafikrak::tier.label');
    }

    public static function getPluralLabel(): string
    {
        return __('trafikrak::tier.plural_label');
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
                    ->label(__('trafikrak::tier.table.name.label'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('trafikrak::tier.table.type.label'))
                    ->formatStateUsing(fn(TierType $state): string => match ($state) {
                        TierType::RELATED_CONTENT_BANNER => 'Banner',
                        TierType::RELATED_CONTENT_COLLECTION => 'Colección',
                        TierType::BOOKSHOP_LATEST => 'Últimos productos Librería',
                        TierType::EDITORIAL_LATEST => 'Últimos productos Editorial',
                        TierType::EDUCATION_UPCOMING => 'Próximos cursos',
                        TierType::EVENTS_UPCOMING => 'Próximos eventos',
                        default => $state->value,
                    })
                    ->badge(),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label(__('trafikrak::tier.table.is_published.label')),
            ])->defaultSort('sort_position')
            ->reorderable('sort_position');
    }

    public static function getDefaultForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('section')
                            ->label(__('trafikrak::tier.form.section.label'))
                            ->required()
                            ->options([
                                Section::HOMEPAGE->value => __('trafikrak::tier.form.section.options.homepage'),
                                Section::BOOKSHOP->value => __('trafikrak::tier.form.section.options.bookshop'),
                                Section::EDITORIAL->value => __('trafikrak::tier.form.section.options.editorial'),
                                Section::EDUCATION->value => __('trafikrak::tier.form.section.options.education'),
                            ]),
                        Forms\Components\TextInput::make('name')
                            ->label(__('trafikrak::tier.form.name.label'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        Forms\Components\Select::make('type')
                            ->label(__('trafikrak::tier.form.type.label'))
                            ->options([
                                TierType::RELATED_CONTENT_BANNER->value => 'Banner',
                                TierType::RELATED_CONTENT_COLLECTION->value => 'Colección',
                                TierType::BOOKSHOP_LATEST->value => 'Últimos productos Librería',
                                TierType::EDITORIAL_LATEST->value => 'Últimos productos Editorial',
                                TierType::EDUCATION_UPCOMING->value => 'Próximos cursos',
                                TierType::EVENTS_UPCOMING->value => 'Próximos eventos',
                            ])
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('banners')
                            ->relationship(titleAttribute: 'name')
                            ->label(__('trafikrak::tier.form.banners.label'))
                            ->multiple()
                            ->preload()
                            ->visible(fn(Get $get) => $get('type') === TierType::RELATED_CONTENT_BANNER->value),
                        Forms\Components\Select::make('collections')
                            ->relationship(
                                modifyQueryUsing: fn(Builder $query)
                                    => $query->whereHas('group',
                                    function (Builder $query) {
                                        $query
                                            ->whereNull('parent_id')
                                            ->whereIn('handle', [
                                                Handle::COLLECTION_GROUP_TAXONOMIES,
                                                Handle::COLLECTION_GROUP_FEATURED,
                                                \Trafikrak\Handle::COLLECTION_GROUP_EDITORIAL_FEATURED,
                                                Handle::COLLECTION_GROUP_ITINERARIES,
                                            ]);
                                    }),
                            )
                            ->getOptionLabelFromRecordUsing(function (Collection $record): string {
                                return "{$record->translateAttribute('name')} [{$record->group->name}]";
                            })
                            ->label(__('trafikrak::tier.form.collections.label'))
                            ->multiple()
                            ->preload()
                            ->hint('taxonomías, destacados, destacados editorial, itinerarios')
                            ->visible(fn(Get $get) => $get('type') === TierType::RELATED_CONTENT_COLLECTION->value),
                        Forms\Components\Toggle::make('is_published')
                            ->label(__('trafikrak::tier.form.is_published.label')),
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
