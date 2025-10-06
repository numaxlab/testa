<?php

namespace Trafikrak\Admin\Filament\Resources\Membership\MembershipTierResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;
use Lunar\Admin\Support\Pages\BaseCreateRecord;
use Trafikrak\Admin\Filament\Resources\Membership\MembershipTierResource;

class CreateMembershipTier extends BaseCreateRecord
{
    use Translatable;

    protected static string $resource = MembershipTierResource::class;

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
