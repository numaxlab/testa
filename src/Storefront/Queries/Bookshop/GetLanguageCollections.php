<?php

namespace Testa\Storefront\Queries\Bookshop;

use Illuminate\Database\Eloquent\Collection;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Collection as LunarCollection;
use NumaxLab\Lunar\Geslib\InterCommands\LanguageCommand;

final class GetLanguageCollections
{
    public function execute(): Collection
    {
        return LunarCollection::whereHas('group', function ($query) {
            $query->where('handle', LanguageCommand::HANDLE);
        })->channel(StorefrontSession::getChannel())
            ->customerGroup(StorefrontSession::getCustomerGroups())
            ->get();
    }
}
