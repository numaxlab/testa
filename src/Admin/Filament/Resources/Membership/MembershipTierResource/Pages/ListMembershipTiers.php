<?php

namespace Trafikrak\Admin\Filament\Resources\Membership\MembershipTierResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;
use Lunar\Admin\Support\Pages\BaseListRecords;
use Trafikrak\Admin\Filament\Resources\Membership\MembershipTierResource;

class ListMembershipTiers extends BaseListRecords
{
    use Translatable;

    protected static string $resource = MembershipTierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
