<?php

namespace Trafikrak\Admin\Filament\Resources\Content;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Lunar\Admin\Support\Resources\BaseResource;
use Trafikrak\Models\Content\Page;
use Trafikrak\Models\Content\Section;

class PageResource extends BaseResource
{
    use Translatable;

    protected static ?string $model = Page::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public static function getNavigationGroup(): ?string
    {
        return __('trafikrak::global.sections.content');
    }

    public static function getLabel(): string
    {
        return __('trafikrak::page.label');
    }

    public static function getPluralLabel(): string
    {
        return __('trafikrak::page.plural_label');
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-text';
    }

    public static function getDefaultSubNavigation(): array
    {
        return [
            PageResource\Pages\EditPage::class,
            PageResource\Pages\ManagePageUrls::class,
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
                    ->label(__('trafikrak::page.table.name.label'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('section')
                    ->label(__('trafikrak::page.table.section.label'))
                    ->badge()
                    ->formatStateUsing(fn (Section $state): string => match ($state) {
                        Section::BOOKSHOP => __('trafikrak::page.form.section.options.bookshop'),
                        Section::EDITORIAL => __('trafikrak::page.form.section.options.editorial'),
                        Section::EDUCATION => __('trafikrak::page.form.section.options.education'),
                        Section::GENERIC => __('trafikrak::page.form.section.options.generic'),
                        default => $state->value,
                    }),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label(__('trafikrak::page.table.is_published.label')),
            ]);
    }

    public static function getDefaultForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('trafikrak::page.form.name.label'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        Forms\Components\Select::make('section')
                            ->label(__('trafikrak::page.form.section.label'))
                            ->options([
                                Section::BOOKSHOP->value => __('trafikrak::page.form.section.options.bookshop'),
                                Section::EDITORIAL->value => __('trafikrak::page.form.section.options.editorial'),
                                Section::EDUCATION->value => __('trafikrak::page.form.section.options.education'),
                                Section::GENERIC->value => __('trafikrak::page.form.section.options.generic'),
                            ])
                            ->required(),
                        Forms\Components\RichEditor::make('intro')
                            ->label(__('trafikrak::page.form.intro.label')),
                        TiptapEditor::make('description')
                            ->label(__('trafikrak::page.form.description.label'))
                            ->profile('default'),
                        Forms\Components\Repeater::make('content')
                            ->label(__('trafikrak::page.form.content.label'))
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('trafikrak::page.form.name.label'))
                                    ->required()
                                    ->maxLength(255),
                                TiptapEditor::make('description')
                                    ->label(__('trafikrak::page.form.description.label'))
                                    ->profile('default'),
                                Forms\Components\Grid::make()
                                    ->columns([
                                        'sm' => 1,
                                        'md' => 2,
                                    ])
                                    ->schema([
                                        Forms\Components\TextInput::make('action')
                                            ->label(__('trafikrak::page.form.content.fields.action.label'))
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('action_tag')
                                            ->label(__('trafikrak::page.form.content.fields.action_tag.label'))
                                            ->maxLength(255),
                                    ]),
                            ])
                            ->defaultItems(0),
                        Forms\Components\Toggle::make('is_published')
                            ->label(__('trafikrak::page.form.is_published.label')),
                    ]),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => PageResource\Pages\ListPages::route('/'),
            'create' => PageResource\Pages\CreatePage::route('/create'),
            'edit' => PageResource\Pages\EditPage::route('/{record}/edit'),
            'urls' => PageResource\Pages\ManagePageUrls::route('/{record}/urls'),
        ];
    }
}
