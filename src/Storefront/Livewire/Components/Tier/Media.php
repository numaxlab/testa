<?php

namespace Testa\Storefront\Livewire\Components\Tier;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Testa\Models\Content\Tier;
use Testa\Storefront\Queries\Content\GetTierMedia;

class Media extends Component
{
    public Tier $tier;

    public Collection $attachments;

    public function mount(): void
    {
        $this->attachments = new GetTierMedia()->execute($this->tier);
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.tier.media');
    }
}
