<?php

namespace Trafikrak\Admin\Filament\Resources\Education\TopicResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;
use Lunar\Admin\Support\Pages\BaseEditRecord;
use Trafikrak\Admin\Filament\Resources\Education\TopicResource;

class EditTopic extends BaseEditRecord
{
    use Translatable;

    protected static string $resource = TopicResource::class;

    public static function getNavigationLabel(): string
    {
        return __('trafikrak::topic.pages.edit.title');
    }

    public function getTitle(): string
    {
        return __('trafikrak::topic.pages.edit.title');
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
                            ->body(__('trafikrak::topic.action.delete.notification.error_protected'))
                            ->send();
                        $action->cancel();
                    }
                }),
        ];
    }
}
