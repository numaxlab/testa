<?php

namespace Testa\Storefront\Queries\News;

use Illuminate\Database\Eloquent\Collection;
use Testa\Models\Attachment;
use Testa\Models\Media\Visibility;
use Testa\Models\News\Event;

final class GetEventAttachments
{
    public function execute(Event $event): Collection
    {
        return Attachment::where('attachable_type', (new Event)->getMorphClass())
            ->where('attachable_id', $event->id)
            ->whereHas('media',
                fn($query) => $query->where('is_published', true)->where('visibility', Visibility::PUBLIC->value))
            ->with('media')
            ->get();
    }
}
