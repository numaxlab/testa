<?php

namespace Testa\Storefront\Livewire\Bookshop;

use Illuminate\View\View;
use Livewire\Attributes\Url;
use Lunar\Models\Collection;
use Lunar\Models\Product;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Livewire\Features\WithPagination;
use Testa\Storefront\Queries\ProductQueryBuilder;

class TopicPage extends Page
{
    use WithPagination;

    public Collection $topic;

    #[Url]
    public string $q = '';

    public function mount($slug): void
    {
        $this->fetchUrl(
            slug: $slug,
            type: (new Collection)->getMorphClass(),
            firstOrFail: true,
            eagerLoad: [
                'element.children',
            ],
        );

        $this->topic = $this->url->element;
    }

    public function render(): View
    {
        $queryBuilder = ProductQueryBuilder::build()
            ->whereHas('collections', function ($query) {
                $query->where(
                    (new Collection)->getTable().'.id',
                    $this->topic->id,
                );
            })
            ->withCount('media')
            ->orderByDesc('media_count');

        if ($this->q) {
            $productsByQuery = Product::search($this->q)->get();

            $queryBuilder->whereIn('id', $productsByQuery->pluck('id'));
        }

        $products = $queryBuilder->paginate(18);

        return view('testa::storefront.livewire.bookshop.topic', compact('products'))
            ->title($this->topic->translateAttribute('name'));
    }

    public function search(): void
    {
        $this->resetPage();
    }
}
