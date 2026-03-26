<?php

namespace Testa\Storefront\Livewire\Components\News;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Testa\Models\News\Event;
use Testa\Storefront\Queries\News\GetEventAttachments;

class EventMedia extends Component
{
    public Event $event;

    public Collection $attachments;

    public function mount(): void
    {
        $this->attachments = new GetEventAttachments()->execute($this->event);
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.news.event-media');
    }
}
