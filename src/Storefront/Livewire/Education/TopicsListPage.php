<?php

namespace Testa\Storefront\Livewire\Education;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Storefront\Queries\Education\GetPublishedTopics;

class TopicsListPage extends Page
{
    public Collection $topics;

    public function mount(): void
    {
        $this->topics = new GetPublishedTopics()->execute();
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.education.topics-list');
    }
}
