<?php

namespace Testa\Admin\Filament\Resources\Config;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
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
                        'group' => __('testa::menu-item.form.type.options.group'),
                        default => $state ?? '',
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
                            ->options(function (?MenuItem $record) {
                                $items = MenuItem::whereNull('parent_id')
                                    ->orderBy('sort_position')
                                    ->with('children')
                                    ->get();

                                $options = [];

                                foreach ($items as $item) {
                                    if ($record && $item->id === $record->id) {
                                        continue;
                                    }
                                    $options[$item->id] = $item->name;

                                    foreach ($item->children as $child) {
                                        if ($record && $child->id === $record->id) {
                                            continue;
                                        }
                                        $options[$child->id] = "— {$child->name}";
                                    }
                                }

                                return $options;
                            })
                            ->placeholder(__('testa::menu-item.form.parent_id.placeholder'))
                            ->live()
                            ->rules([
                                fn() => function (string $attribute, $value, $fail) {
                                    if (! $value) {
                                        return;
                                    }

                                    $parent = MenuItem::find($value);

                                    if ($parent?->parent_id) {
                                        $grandparent = MenuItem::find($parent->parent_id);
                                        if ($grandparent?->parent_id) {
                                            $fail(__('testa::menu-item.form.parent_id.max_depth_error'));
                                        }
                                    }
                                },
                            ]),

                        Select::make('type')
                            ->label(__('testa::menu-item.form.type.label'))
                            ->options(function (Get $get) {
                                $options = [
                                    'manual' => __('testa::menu-item.form.type.options.manual'),
                                    'route' => __('testa::menu-item.form.type.options.route'),
                                    'page' => __('testa::menu-item.form.type.options.page'),
                                    'collection' => __('testa::menu-item.form.type.options.collection'),
                                    'group' => __('testa::menu-item.form.type.options.group'),
                                ];

                                $parentId = $get('parent_id');

                                if ($parentId) {
                                    $parent = MenuItem::find($parentId);
                                    if ($parent && $parent->parent_id === null) {
                                        $options['group'] = __('testa::menu-item.form.type.options.group');
                                    }
                                }

                                return $options;
                            })
                            ->afterStateHydrated(function (Select $component, ?string $state, ?MenuItem $record) {
                                if ($state === 'model' && $record) {
                                    $component->state(match ($record->linkable_type) {
                                        Collection::class => 'collection',
                                        default => 'page',
                                    });
                                }
                            })
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                match ($state) {
                                    'page' => $set('linkable_type', Page::class),
                                    'collection' => $set('linkable_type', Collection::class),
                                    default => $set('linkable_type', null),
                                };
                                $set('linkable_id', null);

                                if (in_array($state, ['page', 'collection', 'group'])) {
                                    $set('link_value', null);
                                }
                            })
                            ->dehydrateStateUsing(fn(?string $state) => in_array($state,
                                ['page', 'collection']) ? 'model' : $state)
                            ->required()
                            ->live(),

                        Forms\Components\Toggle::make('is_published')
                            ->label(__('testa::menu-item.form.is_published.label')),
                    ])->columns(2),

                Forms\Components\Section::make(__('testa::menu-item.sections.link.label'))
                    ->schema([
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

                        Forms\Components\Hidden::make('linkable_type'),

                        Select::make('linkable_id')
                            ->label(__('testa::menu-item.form.linkable_id.label'))
                            ->options(function (Get $get) {
                                $type = $get('type');

                                if ($type === 'page') {
                                    return Page::query()
                                        ->get()->sortBy('name')
                                        ->pluck('name', 'id');
                                }

                                if ($type === 'collection') {
                                    return Collection::query()
                                        ->whereHas('group', function (Builder $query) {
                                            $query
                                                ->whereIn('handle', [
                                                    Handle::COLLECTION_GROUP_TAXONOMIES,
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

                                return [];
                            })
                            ->searchable()
                            ->visible(fn(Get $get) => in_array($get('type'), ['page', 'collection'])),
                    ])
                    ->visible(fn(Get $get) => $get('type') !== 'group'),
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
