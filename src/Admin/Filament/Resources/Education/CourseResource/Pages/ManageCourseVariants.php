<?php

namespace Trafikrak\Admin\Filament\Resources\Education\CourseResource\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Table;
use Lunar\Admin\Support\Pages\BaseManageRelatedRecords;
use Trafikrak\Admin\Filament\Resources\Education\CourseResource;

class ManageCourseVariants extends BaseManageRelatedRecords
{
    protected static string $resource = CourseResource::class;

    protected static string $relationship = 'variants';

    public static function getNavigationIcon(): ?string
    {
        return FilamentIcon::resolve('lunar::product-pricing');
    }

    public static function getNavigationLabel(): string
    {
        return __('lunarpanel::relationmanagers.pricing.title');
    }

    public function getTitle(): string
    {
        return __('lunarpanel::relationmanagers.pricing.title');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table;
    }

    protected function getDefaultHeaderWidgets(): array
    {
        return [
            CourseResource\Widgets\CourseVariantsWidget::class,
        ];
    }
}
