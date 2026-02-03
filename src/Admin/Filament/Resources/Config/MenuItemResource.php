<?php

namespace Testa\Admin\Filament\Resources\Config;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Lunar\Admin\Support\Resources\BaseResource;
use Lunar\Models\Collection;
use NumaxLab\Lunar\Geslib\Handle;
use NumaxLab\Lunar\Geslib\InterCommands\CollectionCommand;
use Testa\Models\Content\Page;
use Testa\Models\MenuItem;

class MenuItemResource extends BaseResource
{
    use Translatable;

    protected static ?string $model = MenuItem::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return __('lunarpanel::global.sections.settings');
    }

    public static function getLabel(): string
    {
        return __('testa::menu-item.label');
    }

    public static function getPluralLabel(): string
    {
        return __('testa::menu-item.plural_label');
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-bars-3';
    }

    public static function getDefaultTable(Table $table): Table
    {
        return $table
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('breadcrumbs')
                    ->label(__('testa::menu-item.table.name.label'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('testa::menu-item.table.type.label'))
                    ->formatStateUsing(fn($state): string => match ($state) {
                        'manual' => __('testa::menu-item.form.type.options.manual'),
                        'route' => __('testa::menu-item.form.type.options.route'),
                        'model' => __('testa::menu-item.form.type.options.model'),
                        default => $state->value,
                    })
                    ->badge(),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label(__('testa::menu-item.table.is_published.label')),
            ])
            ->reorderable('sort_position')
            ->defaultSort('sort_position', 'asc');
    }

    public static function getDefaultForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('testa::menu-item.sections.main.label'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('testa::menu-item.form.name.label'))
                            ->required(),

                        Select::make('parent_id')
                            ->label(__('testa::menu-item.form.parent_id.label'))
                            ->relationship('parent', 'name', fn($query) => $query->whereNull('parent_id'))
                            ->placeholder(__('testa::menu-item.form.parent_id.placeholder')),

                        Forms\Components\Toggle::make('is_published')
                            ->label(__('testa::menu-item.form.is_published.label')),
                    ])->columns(2),

                Forms\Components\Section::make(__('testa::menu-item.sections.link.label'))
                    ->schema([
                        Select::make('type')
                            ->label(__('testa::menu-item.form.type.label'))
                            ->options([
                                'manual' => __('testa::menu-item.form.type.options.manual'),
                                'route' => __('testa::menu-item.form.type.options.route'),
                                'model' => __('testa::menu-item.form.type.options.model'),
                            ])
                            ->required()
                            ->live(),

                        TextInput::make('link_value')
                            ->label(__('testa::menu-item.form.url.label'))
                            ->placeholder('https://...')
                            ->visible(fn(Get $get) => $get('type') === 'manual'),

                        Select::make('link_value')
                            ->label(__('testa::menu-item.form.route.label'))
                            ->options(fn()
                                => collect(Route::getRoutes())
                                ->filter(function ($route) {
                                    $name = $route->getName();
                                    return $name &&
                                        str_starts_with($name, 'testa.') &&
                                        ! str_contains($route->uri(), '{');
                                })
                                ->mapWithKeys(fn($route,
                                )
                                    => [
                                    $route->getName() => __('testa::menu-item.form.route.options.'.str_replace('.', '_',
                                            $route->getName())),
                                ])
                                ->toArray())
                            ->visible(fn(Get $get) => $get('type') === 'route')
                            ->searchable(),

                        Forms\Components\Group::make([
                            Select::make('linkable_type')
                                ->label(__('testa::menu-item.form.linkable_type.label'))
                                ->options([
                                    Page::class => __('testa::menu-item.form.linkable_type.options.page'),
                                    Collection::class => __('testa::menu-item.form.linkable_type.options.collection'),
                                ])
                                ->live(),

                            Select::make('linkable_id')
                                ->label(__('testa::menu-item.form.linkable_id.label'))
                                ->options(function (Get $get) {
                                    $modelClass = $get('linkable_type');

                                    if (! $modelClass) {
                                        return [];
                                    }

                                    $queryBuilder = $modelClass::query();

                                    if ($modelClass === Page::class) {
                                        return $queryBuilder
                                            ->get()->sortBy('name')
                                            ->pluck('name', 'id');
                                    }

                                    if ($modelClass === Collection::class) {
                                        return $queryBuilder
                                            ->whereHas('group', function (Builder $query) {
                                                $query
                                                    ->whereIn('handle', [
                                                        Handle::COLLECTION_GROUP_TAXONOMIES,
                                                        Handle::COLLECTION_GROUP_FEATURED,
                                                        \Testa\Handle::COLLECTION_GROUP_EDITORIAL_FEATURED,
                                                        Handle::COLLECTION_GROUP_ITINERARIES,
                                                        CollectionCommand::HANDLE,
                                                    ]);
                                            })->get()
                                            ->sortBy(fn($record) => $record->translateAttribute('name'))
                                            ->pluck(fn($record,
                                            )
                                                => "{$record->translateAttribute('name')} [{$record->group->name}]",
                                                'id')
                                            ->toArray();
                                    }
                                })
                                ->searchable()
                                ->visible(fn(Get $get) => filled($get('linkable_type'))),
                        ])
                            ->visible(fn(Get $get) => $get('type') === 'model')
                            ->columns(2),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => MenuItemResource\Pages\ListMenuItems::route('/'),
            'create' => MenuItemResource\Pages\CreateMenuItem::route('/create'),
            'edit' => MenuItemResource\Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }
}
