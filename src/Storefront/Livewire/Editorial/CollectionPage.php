<?php

namespace Testa\Storefront\Livewire\Editorial;

use Illuminate\View\View;
use Lunar\Models\Collection;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Livewire\Features\WithPagination;
use Testa\Storefront\Queries\ProductQueryBuilder;

class CollectionPage extends Page
{
    use WithPagination;

    public Collection $collection;

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

        $this->collection = $this->url->element;
    }

    public function render(): View
    {
        $products = ProductQueryBuilder::build()
            ->whereHas('collections', function ($query) {
                $query->where((new Collection)->getTable().'.id', $this->collection->id);
            })
            ->paginate(18);

        return view('testa::storefront.livewire.editorial.collection', compact('products'))
            ->title($this->collection->translateAttribute('name'));
    }
}
