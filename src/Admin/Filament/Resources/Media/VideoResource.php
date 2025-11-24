<?php

namespace Trafikrak\Admin\Filament\Resources\Media;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;
use Lunar\Admin\Support\Resources\BaseResource;
use Trafikrak\Models\Media\Video;
use Trafikrak\Models\Media\Visibility;

class VideoResource extends BaseResource
{
    use Translatable;

    protected static ?string $model = Video::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public static function getNavigationGroup(): ?string
    {
        return __('trafikrak::global.sections.media');
    }

    public static function getLabel(): string
    {
        return __('trafikrak::video.label');
    }

    public static function getPluralLabel(): string
    {
        return __('trafikrak::video.plural_label');
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-video-camera';
    }

    public static function getDefaultSubNavigation(): array
    {
        return [
            VideoResource\Pages\EditVideo::class,
            VideoResource\Pages\ManageVideoUrls::class,
        ];
    }

    public static function getDefaultTable(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('trafikrak::video.table.name.label')),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label(__('trafikrak::video.table.is_published.label')),
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
                            ->label(__('trafikrak::video.form.name.label'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        Forms\Components\RichEditor::make('description')
                            ->label(__('trafikrak::video.form.description.label')),
                        Forms\Components\Grid::make()
                            ->columns([
                                'sm' => 1,
                                'md' => 2,
                            ])
                            ->schema([
                                Forms\Components\Select::make('source')
                                    ->label(__('trafikrak::video.form.source.label'))
                                    ->options([
                                        'youtube' => __('trafikrak::video.form.source.options.youtube'),
                                        'vimeo' => __('trafikrak::video.form.source.options.vimeo'),
                                    ])
                                    ->required(),
                                Forms\Components\Textarea::make('source_id')
                                    ->label(__('trafikrak::video.form.source_id.label'))
                                    ->required(),
                            ]),
                        Forms\Components\Select::make('visibility')
                            ->label(__('trafikrak::video.form.visibility.label'))
                            ->required()
                            ->options([
                                Visibility::PUBLIC->value => __(
                                    'trafikrak::video.form.visibility.options.public',
                                ),
                                Visibility::PRIVATE->value => __(
                                    'trafikrak::video.form.visibility.options.private',
                                ),
                            ]),
                        Forms\Components\Toggle::make('is_published')
                            ->label(__('trafikrak::video.form.is_published.label')),
                    ]),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => VideoResource\Pages\ListVideos::route('/'),
            'create' => VideoResource\Pages\CreateVideo::route('/create'),
            'edit' => VideoResource\Pages\EditVideo::route('/{record}/edit'),
            'urls' => VideoResource\Pages\ManageVideoUrls::route('/{record}/urls'),
        ];
    }
}
