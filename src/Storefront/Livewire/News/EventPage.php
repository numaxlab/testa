<?php

namespace Testa\Storefront\Livewire\News;

use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Models\News\Event;

class EventPage extends Page
{
    public Event $event;

    public function mount($slug): void
    {
        $this->fetchUrl(
            slug: $slug,
            type: (new Event)->getMorphClass(),
            firstOrFail: true,
            eagerLoad: [
                'element.eventType',
                'element.speakers',
                'element.media',
            ],
        );

        $this->event = $this->url->element;
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.news.event')
            ->title($this->event->name);
    }
}
