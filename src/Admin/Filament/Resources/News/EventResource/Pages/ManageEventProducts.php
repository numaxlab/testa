<?php

namespace Trafikrak\Admin\Filament\Resources\News\EventResource\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Lunar\Admin\Filament\Resources\ProductResource;
use Lunar\Admin\Support\Pages\BaseManageRelatedRecords;
use Lunar\Models\Contracts\Product as ProductContract;
use Lunar\Models\Product;
use Trafikrak\Admin\Filament\Resources\News\EventResource;

class ManageEventProducts extends BaseManageRelatedRecords
{
    protected static string $resource = EventResource::class;

    protected static string $relationship = 'products';

    public static function getNavigationIcon(): ?string
    {
        return FilamentIcon::resolve('lunar::products');
    }

    public static function getNavigationLabel(): string
    {
        return __('trafikrak::course.pages.products.label');
    }

    public function getTitle(): string
    {
        return __('trafikrak::course.pages.products.label');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->reorderable('position')
            ->columns([
                ProductResource::getNameTableColumn()->searchable()
                    ->url(function (Model $record) {
                        return ProductResource::getUrl('edit', [
                            'record' => $record->getKey(),
                        ]);
                    }),
                ProductResource::getSkuTableColumn(),
            ])->actions([
                DetachAction::make()
                    ->action(function (Model $record, Table $table) {
                        $relationship = Relation::noConstraints(fn () => $table->getRelationship());

                        $relationship->detach($record);

                        Notification::make()
                            ->success()
                            ->body(__('trafikrak::course.pages.products.actions.detach.notification.success'))
                            ->send();
                    }),
            ])->headerActions([
                AttachAction::make()
                    ->label(
                        __('trafikrak::course.pages.products.actions.attach.label'),
                    )
                    ->form([
                        Forms\Components\Select::make('recordId')
                            ->label(
                                __('trafikrak::course.pages.products.actions.attach.form.record_id.label'),
                            )
                            ->required()
                            ->searchable()
                            ->getSearchResultsUsing(
                                static function (Forms\Components\Select $component, string $search): array {
                                    return Product::search($search)
                                        ->get()
                                        ->mapWithKeys(
                                            fn (ProductContract $record): array
                                                => [
                                                $record->getKey() => $record->translateAttribute('name'),
                                            ],
                                        )
                                        ->all();
                                },
                            ),
                    ])
                    ->action(function (array $arguments, array $data, Form $form, Table $table) {
                        $relationship = Relation::noConstraints(fn () => $table->getRelationship());

                        $product = Product::find($data['recordId']);

                        $relationship->attach($product, [
                            'position' => $relationship->count() + 1,
                        ]);

                        Notification::make()
                            ->success()
                            ->body(__('trafikrak::course.pages.products.actions.attach.notification.success'))
                            ->send();
                    }),
            ]);
    }
}
