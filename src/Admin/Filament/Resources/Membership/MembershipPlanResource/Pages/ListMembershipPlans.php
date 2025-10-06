<?php

namespace Trafikrak\Admin\Filament\Resources\Membership\MembershipPlanResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;
use Lunar\Admin\Support\Pages\BaseListRecords;
use Trafikrak\Admin\Filament\Resources\Membership\MembershipPlanResource;

class ListMembershipPlans extends BaseListRecords
{
    use Translatable;

    protected static string $resource = MembershipPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
