<?php

namespace Trafikrak\Storefront\Livewire\Components\Education;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Trafikrak\Models\Education\Topic;

class FeaturedTopics extends Component
{
    public Collection $topics;

    public function mount(): void
    {
        $this->topics = Topic::where('is_published', true)
            ->with([
                'media',
                'defaultUrl',
            ])
            ->get();
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.components.education.featured-topics');
    }
}
