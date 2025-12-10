<?php

namespace Testa\Storefront\Livewire\Components;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Product;
use Testa\Models\Education\Course;
use Testa\Models\Media\Audio;
use Testa\Models\Media\Video;
use Testa\Storefront\GlobalSearch\GlobalSearch;

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
        return view('testa::storefront.livewire.components.search');
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
            (new Product)->searchableAs() => 'testa.storefront.bookshop.search',
            (new Course)->searchableAs() => 'testa.storefront.education.courses.index',
            (new Audio)->searchableAs(), (new Video)->searchableAs() => 'testa.storefront.media.search',
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
