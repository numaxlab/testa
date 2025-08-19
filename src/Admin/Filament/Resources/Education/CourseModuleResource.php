<?php

namespace Trafikrak\Admin\Filament\Resources\Education;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Concerns\Translatable;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Lunar\Admin\Support\Resources\BaseResource;
use Trafikrak\Models\Education\CourseModule;

class CourseModuleResource extends BaseResource
{
    use Translatable;

    protected static ?string $model = CourseModule::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public static function getLabel(): string
    {
        return __('trafikrak::coursemodule.label');
    }

    public static function getPluralLabel(): string
    {
        return __('trafikrak::coursemodule.plural_label');
    }

    public static function getNavigationIcon(): ?string
    {
        return FilamentIcon::resolve('trafikrak::course-module');
    }

    public static function getNavigationParentItem(): ?string
    {
        return __('trafikrak::course.plural_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('trafikrak::global.sections.education');
    }

    public static function getDefaultSubNavigation(): array
    {
        return [
            CourseModuleResource\Pages\EditCourseModule::class,
            CourseModuleResource\Pages\ManageCourseModuleUrls::class,
            CourseModuleResource\Pages\ManageCourseModuleInstructors::class,
            CourseModuleResource\Pages\ManageCourseModuleAttachments::class,
        ];
    }

    public static function getDefaultTable(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('course.name')
                    ->label(__('trafikrak::coursemodule.table.course_name.label')),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('trafikrak::coursemodule.table.name.label')),
                Tables\Columns\TextColumn::make('starts_at')
                    ->dateTime()
                    ->label(__('trafikrak::coursemodule.table.starts_at.label')),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label(__('trafikrak::coursemodule.table.is_published.label')),
            ])
            ->filters([
                SelectFilter::make('course')
                    ->relationship('course', 'name')
                    ->searchable(['name', 'subtitle'])
                    ->label(__('trafikrak::coursemodule.table.course_name.label')),
            ])
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
                        Forms\Components\Select::make('course_id')
                            ->relationship('course', 'name')
                            ->searchable(['name', 'subtitle'])
                            ->required()
                            ->label(__('trafikrak::coursemodule.form.course_id.label')),
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
                            ->label(__('trafikrak::coursemodule.form.starts_at.label'))
                            ->required(),
                        Forms\Components\Toggle::make('is_published')
                            ->label(__('trafikrak::coursemodule.form.is_published.label')),
                    ]),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => CourseModuleResource\Pages\ListCourseModules::route('/'),
            'create' => CourseModuleResource\Pages\CreateCourseModule::route('/create'),
            'edit' => CourseModuleResource\Pages\EditCourseModule::route('/{record}/edit'),
            'urls' => CourseModuleResource\Pages\ManageCourseModuleUrls::route('/{record}/urls'),
            'instructors' => CourseModuleResource\Pages\ManageCourseModuleInstructors::route('/{record}/instructors'),
            'attachments' => CourseModuleResource\Pages\ManageCourseModuleAttachments::route('/{record}/attachments'),
        ];
    }
}
