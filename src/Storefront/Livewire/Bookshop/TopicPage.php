<?php

namespace Testa\Storefront\Livewire\Bookshop;

use Illuminate\View\View;
use Livewire\Attributes\Url;
use Lunar\Models\Collection;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Livewire\Features\WithPagination;
use Testa\Storefront\Queries\Bookshop\GetTopicProducts;

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
        $products = new GetTopicProducts()->execute($this->topic, $this->q);

        return view('testa::storefront.livewire.bookshop.topic', compact('products'))
            ->title($this->topic->translateAttribute('name'));
    }

    public function search(): void
    {
        $this->resetPage();
    }
}
