<?php

namespace Trafikrak\Storefront\Livewire;

use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page as PageComponent;
use Trafikrak\Models\Content\Page;

class PagePage extends PageComponent
{

    public Page $page;

    public function mount($slug): void
    {
        $this->fetchUrl(
            slug: $slug,
            type: (new Page)->getMorphClass(),
            firstOrFail: true,
        );

        $this->page = $this->url->element;
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.page')
            ->title($this->page->name.' | '.$this->page->human_section);
    }
}
