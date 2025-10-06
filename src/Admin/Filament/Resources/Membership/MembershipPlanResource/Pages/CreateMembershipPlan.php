<?php

namespace Trafikrak\Admin\Filament\Resources\Membership\MembershipPlanResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;
use Lunar\Admin\Support\Pages\BaseCreateRecord;
use Trafikrak\Admin\Filament\Resources\Membership\MembershipPlanResource;

class CreateMembershipPlan extends BaseCreateRecord
{
    use Translatable;

    protected static string $resource = MembershipPlanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
}
