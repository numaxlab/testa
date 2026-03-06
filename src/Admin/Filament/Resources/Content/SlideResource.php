<?php

namespace Testa\Admin\Filament\Resources\Content;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Lunar\Admin\Support\Resources\BaseResource;
use Testa\Models\Content\Section;
use Testa\Models\Content\Slide;

class SlideResource extends BaseResource
{
    use Translatable;

    protected static ?string $model = Slide::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public static function getNavigationGroup(): ?string
    {
        return __('testa::global.sections.content');
    }

    public static function getLabel(): string
    {
        return __('testa::slide.label');
    }

    public static function getPluralLabel(): string
    {
        return __('testa::slide.plural_label');
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-square-2-stack';
    }

    public static function getDefaultSubNavigation(): array
    {
        return [
            SlideResource\Pages\EditSlide::class,
            SlideResource\Pages\ManageSlideMedia::class,
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
                    ->label(__('testa::slide.table.name.label'))
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label(__('testa::slide.table.is_published.label')),
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
                        Forms\Components\TextInput::make('name')
                            ->label(__('testa::slide.form.name.label'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        Forms\Components\Select::make('section')
                            ->label(__('testa::slide.form.section.label'))
                            ->options([
                                Section::HOMEPAGE->value => __('testa::slide.form.section.options.homepage'),
                                Section::BOOKSHOP->value => __('testa::slide.form.section.options.bookshop'),
                                Section::EDITORIAL->value => __('testa::slide.form.section.options.editorial'),
                                Section::EDUCATION->value => __('testa::slide.form.section.options.education'),
                            ])
                            ->required(),
                        Forms\Components\Select::make('style')
                            ->label(__('testa::slide.form.style.label'))
                            ->options([
                                'positive' => __('testa::slide.form.style.options.positive'),
                                'negative' => __('testa::slide.form.style.options.negative'),
                            ])
                            ->required(),
                        Forms\Components\RichEditor::make('description')
                            ->label(__('testa::slide.form.description.label')),
                        Forms\Components\Grid::make()
                            ->columns([
                                'sm' => 1,
                                'md' => 2,
                            ])
                            ->schema([
                                Forms\Components\TextInput::make('link')
                                    ->label(__('testa::banner.form.link.label'))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('button_text')
                                    ->label(__('testa::banner.form.button_text.label'))
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Toggle::make('is_published')
                            ->label(__('testa::slide.form.is_published.label')),
                    ]),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => SlideResource\Pages\ListSlides::route('/'),
            'create' => SlideResource\Pages\CreateSlide::route('/create'),
            'edit' => SlideResource\Pages\EditSlide::route('/{record}/edit'),
            'media' => SlideResource\Pages\ManageSlideMedia::route('/{record}/media'),
        ];
    }
}
