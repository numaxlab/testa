<?php

namespace Testa\Admin\Filament\Resources\Education\CourseResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;
use Illuminate\Database\Eloquent\Model;
use Lunar\Admin\Support\Pages\BaseEditRecord;
use Testa\Admin\Filament\Resources\Education\CourseResource;

class EditCourse extends BaseEditRecord
{
    use Translatable;

    protected static string $resource = CourseResource::class;

    public static function getNavigationLabel(): string
    {
        return __('testa::course.pages.edit.title');
    }

    public function getTitle(): string
    {
        return __('testa::course.pages.edit.title');
    }

    // Filament skips the updated event when no attributes change. CourseObserver::updated
    // must always fire so it can create missing product variants (e.g. when education-rate
    // options are added after the course was created). touch() makes the model dirty and
    // fires CourseObserver
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->fill($data);

        if (!$record->isDirty()) {
            $record->touch();
        }

        $record->save();

        return $record;
    }

    protected function getDefaultHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make()
                ->before(function ($record, Actions\DeleteAction $action) {
                    if ($record->modules->count() > 0) {
                        Notification::make()
                            ->warning()
                            ->body(__('testa::course.action.delete.notification.error_protected'))
                            ->send();
                        $action->cancel();
                    }
                }),
        ];
    }
}
