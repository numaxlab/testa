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
use Testa\Models\Content\Banner;
use Testa\Models\Content\BannerType;
use Testa\Models\Content\Location;

class BannerResource extends BaseResource
{
    use Translatable;

    protected static ?string $model = Banner::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public static function getNavigationGroup(): ?string
    {
        return __('testa::global.sections.content');
    }

    public static function getLabel(): string
    {
        return __('testa::banner.label');
    }

    public static function getPluralLabel(): string
    {
        return __('testa::banner.plural_label');
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-photo';
    }

    public static function getDefaultSubNavigation(): array
    {
        return [
            BannerResource\Pages\EditBanner::class,
            BannerResource\Pages\ManageBannerMedia::class,
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
                    ->label(__('testa::banner.table.name.label'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('testa::banner.table.type.label'))
                    ->badge(),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label(__('testa::banner.table.is_published.label')),
            ]);
    }

    public static function getDefaultForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('testa::banner.form.name.label'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        Forms\Components\RichEditor::make('description')
                            ->label(__('testa::banner.form.description.label')),
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
                        Forms\Components\Select::make('type')
                            ->label(__('testa::banner.form.type.label'))
                            ->options([
                                BannerType::FULL_WIDTH->value => __('testa::banner.form.type.options.full_width'),
                                BannerType::CONTAINED->value => __('testa::banner.form.type.options.contained'),
                            ])
                            ->required(),
                        Forms\Components\Select::make('style')
                            ->label(__('testa::banner.form.style.label'))
                            ->options([
                                'positive' => __('testa::banner.form.style.options.positive'),
                                'negative' => __('testa::banner.form.style.options.negative'),
                            ])
                            ->required(),
                        Forms\Components\Select::make('locations')
                            ->label(__('testa::banner.form.locations.label'))
                            ->options([
                                Location::USER_DASHBOARD_SUBSCRIPTIONS->value => __('testa::banner.form.locations.options.user_dashboard_subscriptions'),
                                Location::COURSE->value => __('testa::banner.form.locations.options.course'),
                                Location::COURSE_REGISTER->value => __('testa::banner.form.locations.options.course_register'),
                            ])
                            ->multiple(),
                        Forms\Components\Toggle::make('is_published')
                            ->label(__('testa::banner.form.is_published.label')),
                    ]),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => BannerResource\Pages\ListBanners::route('/'),
            'create' => BannerResource\Pages\CreateBanner::route('/create'),
            'edit' => BannerResource\Pages\EditBanner::route('/{record}/edit'),
            'media' => BannerResource\Pages\ManageBannerMedia::route('/{record}/media'),
        ];
    }
}
