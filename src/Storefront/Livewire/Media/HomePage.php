<?php

namespace Testa\Storefront\Livewire\Media;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Models\Content\Section;
use Testa\Storefront\Queries\Content\GetTiersBySection;
use Testa\Storefront\Queries\Education\GetPublishedTopics;

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
        $this->tiers = new GetTiersBySection()->execute(Section::MEDIA);
    }

    public function render(): View
    {
        $topics = new GetPublishedTopics()->execute();

        return view('testa::storefront.livewire.media.homepage', compact('topics'))
            ->title(__('Mediateca'));
    }

    public function search(): void
    {
        $this->redirect(
            route(
                'testa.storefront.media.search',
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
