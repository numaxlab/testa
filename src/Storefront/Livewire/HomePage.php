<?php

namespace Trafikrak\Storefront\Livewire;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Models\Content\Section;
use Trafikrak\Models\Content\Tier;

class HomePage extends Page
{
    public ?Collection $tiers;

    public function mount(): void
    {
        $this->tiers = Tier::where('section', Section::HOMEPAGE->value)
            ->where('is_published', true)
            ->orderBy('sort_position')
            ->get();
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.homepage');
    }
}
