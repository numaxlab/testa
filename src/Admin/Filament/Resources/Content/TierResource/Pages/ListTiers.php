<?php

namespace Trafikrak\Admin\Filament\Resources\Content\TierResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Lunar\Admin\Support\Pages\BaseListRecords;
use Trafikrak\Admin\Filament\Resources\Content\TierResource;
use Trafikrak\Models\Content\Section;

class ListTiers extends BaseListRecords
{
    use Translatable;

    protected static string $resource = TierResource::class;

    public function getDefaultTabs(): array
    {
        return [
            Section::HOMEPAGE->value => Tab::make(__('trafikrak::tier.sections.homepage'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('section', Section::HOMEPAGE->value)),
            Section::BOOKSHOP->value => Tab::make(__('trafikrak::tier.sections.bookshop'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('section', Section::BOOKSHOP->value)),
            Section::EDITORIAL->value => Tab::make(__('trafikrak::tier.sections.editorial'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('section', Section::EDITORIAL->value)),
            Section::EDUCATION->value => Tab::make(__('trafikrak::tier.sections.education'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('section', Section::EDUCATION->value)),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
