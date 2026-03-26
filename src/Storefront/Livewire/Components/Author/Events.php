<?php

namespace Testa\Storefront\Livewire\Components\Author;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use NumaxLab\Lunar\Geslib\Models\Author;
use Testa\Storefront\Queries\Editorial\GetAuthorCourseModules;

class Events extends Component
{
    public Author $author;

    public Collection $events;

    public function mount(): void
    {
        // Events query and merge ordered by date...

        $this->events = new GetAuthorCourseModules()->execute($this->author);
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.author.events');
    }
}
