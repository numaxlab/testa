<?php

namespace Trafikrak\Storefront\Livewire\Components\News;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Trafikrak\Models\Attachment;
use Trafikrak\Models\News\Event;

class EventMedia extends Component
{
    public Event $event;

    public Collection $attachments;

    public function mount(): void
    {
        $this->attachments = Attachment::where('attachable_type', (new Event)->getMorphClass())
            ->where('attachable_id', $this->event->id)
            ->whereHas('media', fn ($query) => $query->where('is_published', true))
            ->with('media')
            ->get();
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.components.news.event-media');
    }
}
