<?php

namespace Trafikrak\Admin\Filament\Resources\Education\CourseResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;
use Lunar\Admin\Support\Pages\BaseEditRecord;
use Trafikrak\Admin\Filament\Resources\Education\CourseResource;

class EditCourse extends BaseEditRecord
{
    use Translatable;

    protected static string $resource = CourseResource::class;

    public static function getNavigationLabel(): string
    {
        return __('trafikrak::course.pages.edit.title');
    }

    public function getTitle(): string
    {
        return __('trafikrak::course.pages.edit.title');
    }

    protected function getDefaultHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make()
                ->before(function ($record, Actions\DeleteAction $action) {
                    if ($record->products->count() > 0) {
                        Notification::make()
                            ->warning()
                            ->body(__('trafikrak::course.action.delete.notification.error_protected'))
                            ->send();
                        $action->cancel();
                    }
                }),
        ];
    }
}
