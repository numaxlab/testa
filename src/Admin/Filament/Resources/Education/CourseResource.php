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
use Lunar\Admin\Support\Resources\BaseResource;
use Trafikrak\Models\Education\Course;
use Trafikrak\Models\Education\CourseDeliveryMethod;

class CourseResource extends BaseResource
{
    use Translatable;

    protected static ?string $model = Course::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public static function getLabel(): string
    {
        return __('trafikrak::course.label');
    }

    public static function getPluralLabel(): string
    {
        return __('trafikrak::course.plural_label');
    }

    public static function getNavigationIcon(): ?string
    {
        return FilamentIcon::resolve('trafikrak::course');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('trafikrak::global.sections.education');
    }

    public static function getDefaultSubNavigation(): array
    {
        return [
            CourseResource\Pages\EditCourse::class,
            CourseResource\Pages\ManageCourseMedia::class,
            CourseResource\Pages\ManageCourseUrls::class,
            CourseResource\Pages\ManageCourseModules::class,
            CourseResource\Pages\ManageCourseProducts::class,
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
                    ->label(__('trafikrak::course.table.name.label')),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label(__('trafikrak::course.table.is_published.label')),
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
                        Forms\Components\RichEditor::make('description')
                            ->label(__('trafikrak::course.form.description.label')),
                        Forms\Components\Grid::make()
                            ->columns([
                                'sm' => 1,
                                'md' => 2,
                            ])
                            ->schema([
                                Forms\Components\DatePicker::make('starts_at')
                                    ->label(__('trafikrak::course.form.starts_at.label'))
                                    ->required(),
                                Forms\Components\DatePicker::make('ends_at')
                                    ->label(__('trafikrak::course.form.ends_at.label'))
                                    ->required(),
                            ]),
                        Forms\Components\Select::make('delivery_method')
                            ->label(__('trafikrak::course.form.delivery_method.label'))
                            ->required()
                            ->options([
                                CourseDeliveryMethod::IN_PERSON->value => __(
                                    'trafikrak::course.form.delivery_method.options.in_person',
                                ),
                                CourseDeliveryMethod::ONLINE->value => __(
                                    'trafikrak::course.form.delivery_method.options.online',
                                ),
                                CourseDeliveryMethod::HYBRID->value => __(
                                    'trafikrak::course.form.delivery_method.options.hybrid',
                                ),
                            ]),
                        Forms\Components\TextInput::make('location')
                            ->label(__('trafikrak::course.form.location.label'))
                            ->maxLength(255),
                        Forms\Components\Select::make('topic_id')
                            ->label(__('trafikrak::course.form.topic_id.label'))
                            ->required()
                            ->relationship('topic', 'name'),
                        Forms\Components\Toggle::make('is_published')
                            ->label(__('trafikrak::course.form.is_published.label')),
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
            'media' => CourseResource\Pages\ManageCourseMedia::route('/{record}/media'),
            'urls' => CourseResource\Pages\ManageCourseUrls::route('/{record}/urls'),
            'modules' => CourseResource\Pages\ManageCourseModules::route('/{record}/modules'),
            'products' => CourseResource\Pages\ManageCourseProducts::route('/{record}/products'),
        ];
    }
}
