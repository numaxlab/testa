<?php

namespace Testa\Admin\Filament\Support\Page;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Lunar\Admin\Support\Pages\BaseManageRelatedRecords;
use Testa\Models\Attachment;
use Testa\Models\Education\Course;
use Testa\Models\Education\CourseModule;
use Testa\Models\News\Event;

class ManageMediaUsagesRelatedRecords extends BaseManageRelatedRecords
{
    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-link';
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->reorderable('position')
            ->columns([
                Tables\Columns\TextColumn::make('attachable.name')
                    ->label(__('testa::attachment.table.attachable.label')),
                Tables\Columns\TextColumn::make('attachable_type')
                    ->label(__('testa::attachment.table.attachable_type.label'))
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        (new Course)->getMorphClass() => __('testa::course.label'),
                        (new CourseModule)->getMorphClass() => __('testa::coursemodule.label'),
                        (new Event)->getMorphClass() => __('testa::event.label'),
                        default => $state,
                    })
                    ->badge(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label(__('testa::attachment.actions.attach_to.label'))
                    ->form([
                        Forms\Components\MorphToSelect::make('attachable')
                            ->label(__('testa::attachment.actions.attach_to.form.attachable.label'))
                            ->searchable()
                            ->required()
                            ->types([
                                Forms\Components\MorphToSelect\Type::make(Course::class)->titleAttribute('name')->label(__('testa::course.label')),
                                Forms\Components\MorphToSelect\Type::make(CourseModule::class)->titleAttribute('name')->label(__('testa::coursemodule.label')),
                                Forms\Components\MorphToSelect\Type::make(Event::class)->titleAttribute('name')->label(__('testa::event.label')),
                            ]),
                    ])
                    ->action(function (array $arguments, array $data, Form $form, Table $table) {
                        $relationship = Relation::noConstraints(fn() => $table->getRelationship());
                        $media = $relationship->getParent();

                        $data['media_type'] = $media->getMorphClass();
                        $data['media_id'] = $media->id;
                        $data['position'] = Attachment::where('attachable_type', $data['attachable_type'])
                                ->where('attachable_id', $data['attachable_id'])
                                ->count() + 1;

                        Attachment::create($data);

                        Notification::make()
                            ->success()
                            ->body(__('testa::attachment.actions.attach_to.notification.success'))
                            ->send();
                    }),
            ])
            ->actions([
                DetachAction::make()
                    ->action(function (Model $record) {
                        $record->delete();

                        Notification::make()
                            ->success()
                            ->body(__('testa::attachment.actions.detach.notification.success'))
                            ->send();
                    }),
            ]);
    }
}
