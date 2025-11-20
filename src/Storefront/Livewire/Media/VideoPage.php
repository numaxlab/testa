<?php

namespace Trafikrak\Storefront\Livewire\Media;

use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Models\Media\Video;

class VideoPage extends Page
{
    public Video $video;

    public function mount($slug): void
    {
        $this->fetchUrl(
            slug: $slug,
            type: (new Video)->getMorphClass(),
            firstOrFail: true,
            eagerLoad: [
                'element.attachments',
                'element.attachments.attachable',
            ],
        );

        $this->video = $this->url->element;
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.media.video')
            ->title($this->video->name);
    }
}
