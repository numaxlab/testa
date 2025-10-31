<?php

namespace Trafikrak\Storefront\Views\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Collection;
use NumaxLab\Lunar\Geslib\Handle;
use NumaxLab\Lunar\Geslib\InterCommands\CollectionCommand;
use Trafikrak\Models\Content\Page;
use Trafikrak\Models\Content\Section;

class Header extends Component
{
    public function __construct() {}

    public function render(): View
    {
        $pages = Page::whereIn('section', [
            Section::BOOKSHOP->value,
            Section::EDITORIAL->value,
            Section::EDUCATION->value,
        ])->where('is_published', true)
            ->get()
            ->groupBy('section');

        $sections = Collection::whereHas('group', function ($query) {
            $query->where('handle', Handle::COLLECTION_GROUP_TAXONOMIES);
        })->whereNull('parent_id')
            ->where('attribute_data->is-section->value', true)
            ->channel(StorefrontSession::getChannel())
            ->customerGroup(StorefrontSession::getCustomerGroups())
            ->has('defaultUrl')
            ->orderBy('_lft', 'ASC')
            ->with(['defaultUrl'])->get();

        $editorialCollections = Collection::whereHas('group', function ($query) {
            $query->where('handle', CollectionCommand::HANDLE);
        })->whereNull('parent_id')
            ->where('attribute_data->is-section->value', true)
            ->channel(StorefrontSession::getChannel())
            ->customerGroup(StorefrontSession::getCustomerGroups())
            ->orderBy('_lft', 'ASC')
            ->get();

        $editorialSpecialCollections = Collection::whereHas('group', function ($query) {
            $query->where('handle', CollectionCommand::HANDLE);
        })->whereNull('parent_id')
            ->where('attribute_data->is-special->value', true)
            ->channel(StorefrontSession::getChannel())
            ->customerGroup(StorefrontSession::getCustomerGroups())
            ->orderBy('_lft', 'ASC')
            ->get();

        return view('trafikrak::storefront.components.header', compact(
            'pages',
            'sections',
            'editorialCollections',
            'editorialSpecialCollections',
        ));
    }
}