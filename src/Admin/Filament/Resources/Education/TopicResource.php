<?php

namespace Trafikrak\Admin\Filament\Resources\Education;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Concerns\Translatable;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Lunar\Admin\Support\Resources\BaseResource;
use Trafikrak\Models\Education\Topic;

class TopicResource extends BaseResource
{
    use Translatable;

    protected static ?string $model = Topic::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public static function getLabel(): string
    {
        return __('trafikrak::topic.label');
    }

    public static function getPluralLabel(): string
    {
        return __('trafikrak::topic.plural_label');
    }

    public static function getNavigationIcon(): ?string
    {
        return FilamentIcon::resolve('lunar::collections');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('trafikrak::global.sections.education');
    }

    public static function getDefaultSubNavigation(): array
    {
        return [
            TopicResource\Pages\EditTopic::class,
            TopicResource\Pages\ManageTopicMedia::class,
            TopicResource\Pages\ManageTopicUrls::class,
        ];
    }

    public static function getDefaultTable(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('thumbnail')
                    ->collection(config('lunar.media.collection'))
                    ->conversion('small')
                    ->limit(1)
                    ->square()
                    ->label(''),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('trafikrak::topic.table.name.label')),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label(__('trafikrak::topic.table.is_published.label')),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->searchable();
    }

    public static function getDefaultForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('trafikrak::course.form.name.label'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        Forms\Components\TextInput::make('subtitle')
                            ->label(__('trafikrak::course.form.subtitle.label'))
                            ->maxLength(255),
                        TiptapEditor::make('description')
                            ->label(__('trafikrak::course.form.description.label'))
                            ->profile('default'),
                        Forms\Components\Toggle::make('is_published')
                            ->label(__('trafikrak::course.form.is_published.label')),
                    ]),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => TopicResource\Pages\ListTopics::route('/'),
            'create' => TopicResource\Pages\CreateTopic::route('/create'),
            'edit' => TopicResource\Pages\EditTopic::route('/{record}/edit'),
            'media' => TopicResource\Pages\ManageTopicMedia::route('/{record}/media'),
            'urls' => TopicResource\Pages\ManageTopicUrls::route('/{record}/urls'),
        ];
    }
}
