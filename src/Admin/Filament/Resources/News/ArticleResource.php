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
use Testa\Models\News\Article;

class ArticleResource extends BaseResource
{
    use Translatable;

    protected static ?string $model = Article::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public static function getNavigationGroup(): ?string
    {
        return __('testa::global.sections.news');
    }

    public static function getLabel(): string
    {
        return __('testa::article.label');
    }

    public static function getPluralLabel(): string
    {
        return __('testa::article.plural_label');
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-newspaper';
    }

    public static function getDefaultSubNavigation(): array
    {
        return [
            ArticleResource\Pages\EditArticle::class,
            ArticleResource\Pages\ManageArticleMedia::class,
            ArticleResource\Pages\ManageArticleUrls::class,
            ArticleResource\Pages\ManageArticleProducts::class,
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
                    ->label(__('testa::article.table.name.label'))
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label(__('testa::article.table.is_published.label')),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getDefaultForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('testa::article.form.name.label'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        Forms\Components\Textarea::make('summary')
                            ->label(__('testa::article.form.summary.label')),
                        TiptapEditor::make('content')
                            ->label(__('testa::article.form.content.label'))
                            ->profile('default'),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label(__('testa::article.form.published_at.label'))
                            ->required(),
                        Forms\Components\Toggle::make('is_published')
                            ->label(__('testa::article.form.is_published.label')),
                    ]),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => ArticleResource\Pages\ListArticles::route('/'),
            'create' => ArticleResource\Pages\CreateArticle::route('/create'),
            'edit' => ArticleResource\Pages\EditArticle::route('/{record}/edit'),
            'urls' => ArticleResource\Pages\ManageArticleUrls::route('/{record}/urls'),
            'media' => ArticleResource\Pages\ManageArticleMedia::route('/{record}/media'),
            'products' => ArticleResource\Pages\ManageArticleProducts::route('/{record}/products'),
        ];
    }
}
