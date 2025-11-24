<?php

namespace Trafikrak\Storefront\Livewire\Media;

use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Models\Media\Audio;

class AudioPage extends Page
{
    public Audio $audio;

    public function mount($slug): void
    {
        $this->fetchUrl(
            slug: $slug,
            type: (new Audio)->getMorphClass(),
            firstOrFail: true,
            eagerLoad: [
                'element.attachments',
                'element.attachments.attachable',
            ],
        );

        $this->audio = $this->url->element;
    }

    public function render(): View
    {
        if (! Gate::authorize('view', $this->audio)) {
            abort(403);
        }

        return view('trafikrak::storefront.livewire.media.audio')
            ->title($this->audio->name);
    }
}
