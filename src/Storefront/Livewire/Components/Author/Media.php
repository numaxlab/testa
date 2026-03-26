<?php

namespace Testa\Storefront\Livewire\Components\Author;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use NumaxLab\Lunar\Geslib\Models\Author;
use Testa\Storefront\Queries\Editorial\GetAuthorCourseModules;
use Testa\Storefront\Queries\Education\GetPublicAttachmentsForModules;

class Media extends Component
{
    public Author $author;

    public Collection $attachments;

    public function mount(): void
    {
        $modules = new GetAuthorCourseModules()->execute($this->author);

        $this->attachments = new GetPublicAttachmentsForModules()->execute($modules);
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.author.media');
    }
}
