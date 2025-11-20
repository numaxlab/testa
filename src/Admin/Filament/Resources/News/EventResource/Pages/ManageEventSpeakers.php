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
use Lunar\Admin\Support\Pages\BaseManageRelatedRecords;
use NumaxLab\Lunar\Geslib\Admin\Filament\Resources\AuthorResource;
use NumaxLab\Lunar\Geslib\Models\Author;
use Trafikrak\Admin\Filament\Resources\News\EventResource;

class ManageEventSpeakers extends BaseManageRelatedRecords
{
    protected static string $resource = EventResource::class;

    protected static string $relationship = 'speakers';

    public static function getNavigationIcon(): ?string
    {
        return FilamentIcon::resolve('lunar::customers');
    }

    public static function getNavigationLabel(): string
    {
        return __('trafikrak::event.pages.speakers.label');
    }

    public function getTitle(): string
    {
        return __('trafikrak::event.pages.speakers.label');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->reorderable('position')
            ->columns([
                AuthorResource::getNameTableColumn()->searchable()
                    ->url(function (Model $record) {
                        return AuthorResource::getUrl('edit', [
                            'record' => $record->getKey(),
                        ]);
                    }),
            ])->actions([
                DetachAction::make()
                    ->action(function (Model $record, Table $table) {
                        $relationship = Relation::noConstraints(fn () => $table->getRelationship());

                        $relationship->detach($record);

                        Notification::make()
                            ->success()
                            ->body(__('trafikrak::coursemodule.pages.instructors.actions.detach.notification.success'))
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
                                __('trafikrak::coursemodule.pages.instructors.actions.attach.form.record_id.label'),
                            )
                            ->required()
                            ->searchable()
                            ->getSearchResultsUsing(
                                static function (Forms\Components\Select $component, string $search): array {
                                    return Author::search($search)
                                        ->get()
                                        ->mapWithKeys(
                                            fn (Author $record): array
                                                => [
                                                $record->getKey() => $record->name,
                                            ],
                                        )
                                        ->all();
                                },
                            ),
                    ])
                    ->action(function (array $arguments, array $data, Form $form, Table $table) {
                        $relationship = Relation::noConstraints(fn () => $table->getRelationship());

                        $instructor = Author::find($data['recordId']);

                        $relationship->attach($instructor, [
                            'position' => $relationship->count() + 1,
                        ]);

                        Notification::make()
                            ->success()
                            ->body(__('trafikrak::coursemodule.pages.instructors.actions.attach.notification.success'))
                            ->send();
                    }),
            ]);
    }
}
