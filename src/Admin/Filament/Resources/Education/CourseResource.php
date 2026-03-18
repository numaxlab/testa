<?php

namespace Testa\Admin\Filament\Resources\Education;

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
use Testa\Models\Education\Course;
use Testa\Models\EventDeliveryMethod;

class CourseResource extends BaseResource
{
    use Translatable;

    protected static ?string $model = Course::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public static function getLabel(): string
    {
        return __('testa::course.label');
    }

    public static function getPluralLabel(): string
    {
        return __('testa::course.plural_label');
    }

    public static function getNavigationIcon(): ?string
    {
        return FilamentIcon::resolve('testa::course');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('testa::global.sections.education');
    }

    public static function getDefaultSubNavigation(): array
    {
        return [
            CourseResource\Pages\EditCourse::class,
            CourseResource\Pages\ManageCourseVariants::class,
            CourseResource\Pages\ManageCourseMedia::class,
            CourseResource\Pages\ManageCourseUrls::class,
            CourseResource\Pages\ManageCourseProducts::class,
            CourseResource\Pages\ManageCourseAttachments::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            CourseResource\Widgets\CourseVariantsWidget::class,
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
                    ->label(__('testa::course.table.name.label')),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label(__('testa::course.table.is_published.label')),
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
                            ->label(__('testa::course.form.name.label'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        Forms\Components\TextInput::make('subtitle')
                            ->label(__('testa::course.form.subtitle.label'))
                            ->maxLength(255),
                        TiptapEditor::make('description')
                            ->label(__('testa::course.form.description.label'))
                            ->profile('default'),
                        Forms\Components\Grid::make()
                            ->columns([
                                'sm' => 1,
                                'md' => 2,
                            ])
                            ->schema([
                                Forms\Components\DatePicker::make('starts_at')
                                    ->label(__('testa::course.form.starts_at.label'))
                                    ->required(),
                                Forms\Components\DatePicker::make('ends_at')
                                    ->label(__('testa::course.form.ends_at.label'))
                                    ->required(),
                            ]),
                        Forms\Components\Select::make('topic_id')
                            ->label(__('testa::course.form.topic_id.label'))
                            ->required()
                            ->relationship('topic', 'name'),
                        Forms\Components\Select::make('delivery_method')
                            ->label(__('testa::coursemodule.form.delivery_method.label'))
                            ->required()
                            ->options([
                                EventDeliveryMethod::IN_PERSON->value => __(
                                    'testa::coursemodule.form.delivery_method.options.in_person',
                                ),
                                EventDeliveryMethod::ONLINE->value => __(
                                    'testa::coursemodule.form.delivery_method.options.online',
                                ),
                                EventDeliveryMethod::HYBRID->value => __(
                                    'testa::coursemodule.form.delivery_method.options.hybrid',
                                ),
                                EventDeliveryMethod::MOOC->value => __(
                                    'testa::coursemodule.form.delivery_method.options.mooc',
                                ),
                            ]),
                        Forms\Components\Textarea::make('alert')
                            ->label(__('testa::event.form.alert.label')),
                        Forms\Components\Toggle::make('is_published')
                            ->label(__('testa::course.form.is_published.label')),
                    ]),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => CourseResource\Pages\ListCourses::route('/'),
            'create' => CourseResource\Pages\CreateCourse::route('/create'),
            'edit' => CourseResource\Pages\EditCourse::route('/{record}/edit'),
            'variants' => CourseResource\Pages\ManageCourseVariants::route('/{record}/variants'),
            'media' => CourseResource\Pages\ManageCourseMedia::route('/{record}/media'),
            'urls' => CourseResource\Pages\ManageCourseUrls::route('/{record}/urls'),
            'products' => CourseResource\Pages\ManageCourseProducts::route('/{record}/products'),
            'attachments' => CourseResource\Pages\ManageCourseAttachments::route('/{record}/attachments'),
        ];
    }
}
