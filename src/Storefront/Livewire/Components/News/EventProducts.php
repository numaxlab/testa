<?php

namespace Testa\Storefront\Livewire\Components\News;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Testa\Models\News\Event;
use Testa\Storefront\Queries\ProductQueryBuilder;

class EventProducts extends Component
{
    public Event $event;

    public Collection $products;

    public function mount(): void
    {
        $this->products = ProductQueryBuilder::fromRelation($this->event->products())->get();
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.news.event-products');
    }
}
