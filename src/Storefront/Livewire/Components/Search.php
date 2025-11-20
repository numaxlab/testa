<?php

namespace Trafikrak\Storefront\Livewire\Components;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Product;
use Trafikrak\Models\Education\Course;
use Trafikrak\Models\Media\Audio;
use Trafikrak\Models\Media\Video;
use Trafikrak\Storefront\GlobalSearch\GlobalSearch;

class Search extends Component
{
    public ?string $query;

    public array $contentTypes = [];

    public ?string $contentTypeFilter = '';

    public Collection $results;

    public int $estimatedTotalHits = 0;

    public function mount(): void
    {
        $this->contentTypes = [
            (new Product)->searchableAs() => __('Libros'),
            (new Course)->searchableAs() => __('Cursos'),
            (new Audio)->searchableAs() => __('Audios'),
            (new Video)->searchableAs() => __('Vídeos'),
        ];

        $this->contentTypeFilter = (new Product)->searchableAs();

        $this->results = collect();
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.components.search');
    }

    public function setContentTypeFilter(GlobalSearch $globalSearch, $contentType): void
    {
        $this->contentTypeFilter = $contentType;
        $this->updatedQuery($globalSearch);
    }

    public function updatedQuery(GlobalSearch $globalSearch): void
    {
        $search = trim($this->query);

        if (blank($search)) {
            $this->results = collect();
            return;
        }

        $globalSearch->setContentType($this->contentTypeFilter);

        $this->results = $globalSearch->getResults($search);
        $this->estimatedTotalHits = $globalSearch->estimatedTotalHits;
    }

    public function search(): void
    {
        $redirectRoute = match ($this->contentTypeFilter) {
            'products' => 'trafikrak.storefront.bookshop.search',
            'courses' => 'trafikrak.storefront.education.courses.index',
            'audios', 'videos' => 'trafikrak.storefront.media.search',
            default => null,
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
