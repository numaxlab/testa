<?php

namespace Testa\Models;

use NumaxLab\Lunar\Geslib\Handle;
use NumaxLab\Lunar\Geslib\InterCommands\CollectionCommand;

class Collection extends \NumaxLab\Lunar\Geslib\Models\Collection
{
    public function getTable()
    {
        return config('lunar.database.table_prefix').'collections';
    }

    public function getUrlAttribute(): string
    {
        return match ($this->group->handle) {
            Handle::COLLECTION_GROUP_TAXONOMIES => $this->getTaxonomyUrl(),
            CollectionCommand::HANDLE => $this->getEditorialCollectionUrl(),
            default => '#',
        };
    }

    private function getTaxonomyUrl(): string
    {
        if (! $this->isInSectionTree()) {
            return route('testa.storefront.bookshop.topics.show', $this->defaultUrl->slug);
        }

        if ($this->isRoot()) {
            return route('testa.storefront.bookshop.sections.show', $this->defaultUrl->slug);
        }

        return route(
            'testa.storefront.bookshop.sections.show',
            [
                'slug' => $this->getAncestorSection()->defaultUrl->slug,
                't' => $this->id,
            ],
        );
    }

    private function getEditorialCollectionUrl(): string
    {
        if ($this->translateAttribute('is-special') === true) {
            return route('testa.storefront.editorial.collections.special.show', $this->defaultUrl->slug);
        }

        return route('testa.storefront.editorial.collections.show', $this->defaultUrl->slug);
    }
}
