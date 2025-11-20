<?php

namespace Trafikrak\Storefront\Livewire\Media;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Models\Content\Section;
use Trafikrak\Models\Content\Tier;
use Trafikrak\Models\Education\Topic;

class HomePage extends Page
{
    public Collection $tiers;

    #[Url]
    public string $q = '';

    #[Url]
    public string $c = '';

    #[Url]
    public string $t = '';

    public function mount(): void
    {
        $this->tiers = Tier::where('section', Section::MEDIA->value)
            ->where('is_published', true)
            ->orderBy('sort_position')
            ->get();
    }

    public function render(): View
    {
        $topics = Topic::where('is_published', true)
            ->with([
                'media',
                'defaultUrl',
            ])
            ->get();

        return view('trafikrak::storefront.livewire.media.homepage', compact('topics'))
            ->title(__('Mediateca'));
    }

    public function search(): void
    {
        $this->redirect(
            route(
                'trafikrak.storefront.media.search',
                parameters: [
                    'q' => $this->q,
                    'c' => $this->c,
                    't' => $this->t,
                ],
                absolute: false,
            ),
            navigate: true,
        );
    }
}
