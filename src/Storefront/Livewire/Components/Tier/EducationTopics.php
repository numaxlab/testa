<?php

namespace Testa\Storefront\Livewire\Components\Tier;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Testa\Models\Content\Tier;
use Testa\Storefront\Queries\Content\GetTierEducationTopics;

class EducationTopics extends Component
{
    public Tier $tier;

    public Collection $topics;

    public function mount(): void
    {
        $this->topics = new GetTierEducationTopics()->execute($this->tier);
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.tier.education-topics');
    }
}
