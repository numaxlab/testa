<?php

namespace Testa\Storefront\Livewire\Bookshop;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Models\Content\Section;
use Testa\Storefront\Queries\Content\GetSlidesBySection;
use Testa\Storefront\Queries\Content\GetTiersBySection;

class HomePage extends Page
{
    public Collection $slides;

    public Collection $tiers;

    public function mount(): void
    {
        $this->slides = new GetSlidesBySection()->execute(Section::BOOKSHOP);
        $this->tiers = new GetTiersBySection()->execute(Section::BOOKSHOP);
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.bookshop.homepage')
            ->title(__('Librería'));
    }
}
