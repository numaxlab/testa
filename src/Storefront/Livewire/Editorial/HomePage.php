<?php

namespace Testa\Storefront\Livewire\Editorial;

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
        $this->slides = new GetSlidesBySection()->execute(Section::EDITORIAL);
        $this->tiers = new GetTiersBySection()->execute(Section::EDITORIAL);
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.editorial.homepage')
            ->title(__('Editorial'));
    }
}
