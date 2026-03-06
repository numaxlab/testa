<?php

namespace Testa\Storefront\Livewire\Bookshop;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Models\Content\Section;
use Testa\Models\Content\Slide;
use Testa\Models\Content\Tier;

class HomePage extends Page
{
    public Collection $slides;

    public Collection $tiers;

    public function mount(): void
    {
        $this->slides = Slide::where('section', Section::BOOKSHOP->value)
            ->where('is_published', true)
            ->orderBy('sort_position')
            ->with('media')
            ->get();

        $this->tiers = Tier::where('section', Section::BOOKSHOP->value)
            ->where('is_published', true)
            ->orderBy('sort_position')
            ->get();
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.bookshop.homepage')
            ->title(__('Librería'));
    }
}
