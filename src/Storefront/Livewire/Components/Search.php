<?php

namespace Trafikrak\Storefront\Livewire\Components;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Product;
use Trafikrak\Models\Education\Course;
use Trafikrak\Storefront\GlobalSearch\GlobalSearch;

class Search extends Component
{
    public ?string $query;

    public array $contentTypes = [];

    public ?string $contentTypeFilter = 'all';

    public Collection $results;

    public int $estimatedTotalHits = 0;

    public function mount(): void
    {
        $this->contentTypes = [
            'all' => __('Todos los resultados'),
            (new Product)->searchableAs() => __('Libros'),
            (new Course)->searchableAs() => __('Cursos'),
        ];

        $this->results = collect();
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.components.search');
    }

    public function setContentTypeFilter($contentType): void
    {
        $this->contentTypeFilter = $contentType;
        $this->updatedQuery();
    }

    public function updatedQuery(): void
    {
        $search = trim($this->query);

        if (blank($search)) {
            $this->results = collect();
            return;
        }

        $globalSearch = app(GlobalSearch::class);

        if ($this->contentTypeFilter !== 'all') {
            $globalSearch->setContentType($this->contentTypeFilter);
        } else {
            $globalSearch->setContentType(null);
        }

        $this->results = $globalSearch->getResults($search);
        $this->estimatedTotalHits = $globalSearch->estimatedTotalHits;
    }

    public function search(): void
    {
        match ($this->contentTypeFilter) {
            'products' => $redirectRoute = 'trafikrak.storefront.bookshop.search',
            'audio', 'video' => $redirectRoute = 'trafikrak.storefront.media.search',
            'courses' => $redirectRoute = 'trafikrak.storefront.education.search',
            default => $redirectRoute = null,
        };

        if ($redirectRoute !== null) {
            $this->redirect(
                route(
                    $redirectRoute,
                    [
                        'q' => $this->query,
                    ],
                ),
                true,
            );
        }
    }
}
