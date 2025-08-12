<?php

namespace Trafikrak\Admin\Filament\Resources\Education\CourseResource\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\Concerns\Translatable;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables;
use Filament\Tables\Table;
use Lunar\Admin\Support\Pages\BaseManageRelatedRecords;
use Trafikrak\Admin\Filament\Resources\Education\CourseResource;

class ManageCourseModules extends BaseManageRelatedRecords
{
    use Translatable;

    protected static string $resource = CourseResource::class;

    protected static string $relationship = 'modules';

    public static function getNavigationIcon(): ?string
    {
        return FilamentIcon::resolve('trafikrak::course-module');
    }

    public static function getNavigationLabel(): string
    {
        return __('trafikrak::course.pages.modules.label');
    }

    public function getTitle(): string
    {
        return __('trafikrak::course.pages.modules.label');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->limit(50)
                    ->label(__('trafikrak::coursemodule.table.name.label')),
                Tables\Columns\TextColumn::make('starts_at')
                    ->dateTime()
                    ->label(__('trafikrak::coursemodule.table.is_published.label')),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label(__('trafikrak::coursemodule.table.is_published.label')),
            ])
            ->headerActions([
                Tables\Actions\LocaleSwitcher::make(),
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('trafikrak::coursemodule.form.name.label'))
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),
                Forms\Components\TextInput::make('subtitle')
                    ->label(__('trafikrak::coursemodule.form.subtitle.label'))
                    ->maxLength(255),
                Forms\Components\RichEditor::make('description')
                    ->label(__('trafikrak::coursemodule.form.description.label')),
                Forms\Components\DateTimePicker::make('starts_at')
                    ->label(__('trafikrak::coursemodule.form.starts_at.label')),
                Forms\Components\Toggle::make('is_published')
                    ->label(__('trafikrak::coursemodule.form.is_published.label')),
            ])
            ->columns(1);
    }
}
