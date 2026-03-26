<?php

namespace Testa\Storefront\Livewire\Media;

use Illuminate\View\View;
use Livewire\Attributes\Url;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Livewire\Features\WithPagination;
use Testa\Storefront\Queries\Education\GetPublishedTopics;
use Testa\Storefront\Queries\Media\GetMediaSearch;

class SearchPage extends Page
{
    use WithPagination;

    #[Url]
    public string $q = '';

    #[Url]
    public string $c = '';

    #[Url]
    public string $t = '';

    public function render(): View
    {
        $topics = new GetPublishedTopics()->execute();

        $media = new GetMediaSearch()->execute($this->q);

        return view('testa::storefront.livewire.media.search', compact('topics', 'media'))
            ->title(__('Audios y vídeos'));
    }

    public function search(): void
    {
        $this->resetPage();
    }
}
