<?php

namespace Trafikrak\Admin\Filament\Resources\Media;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;
use Lunar\Admin\Support\Resources\BaseResource;
use Trafikrak\Models\Media\Document;

class DocumentResource extends BaseResource
{
    use Translatable;

    protected static ?string $model = Document::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public static function getNavigationGroup(): ?string
    {
        return __('trafikrak::global.sections.media');
    }

    public static function getLabel(): string
    {
        return __('trafikrak::document.label');
    }

    public static function getPluralLabel(): string
    {
        return __('trafikrak::document.plural_label');
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-text';
    }

    public static function getDefaultSubNavigation(): array
    {
        return [
            DocumentResource\Pages\EditDocument::class,
            DocumentResource\Pages\ManageDocumentUrls::class,
        ];
    }

    public static function getDefaultTable(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('trafikrak::document.table.name.label')),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label(__('trafikrak::document.table.is_published.label')),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->searchable();
    }

    public static function getDefaultForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('trafikrak::document.form.name.label'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        Forms\Components\RichEditor::make('description')
                            ->label(__('trafikrak::document.form.description.label')),
                        Forms\Components\FileUpload::make('path')
                            ->label(__('trafikrak::document.form.path.label'))
                            ->directory('audios')
                            ->required(),
                        Forms\Components\Toggle::make('is_published')
                            ->label(__('trafikrak::document.form.is_published.label')),
                    ]),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => DocumentResource\Pages\ListDocuments::route('/'),
            'create' => DocumentResource\Pages\CreateDocument::route('/create'),
            'edit' => DocumentResource\Pages\EditDocument::route('/{record}/edit'),
            'urls' => DocumentResource\Pages\ManageDocumentUrls::route('/{record}/urls'),
        ];
    }
}
