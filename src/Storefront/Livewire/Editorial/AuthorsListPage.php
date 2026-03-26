<?php

namespace Testa\Storefront\Livewire\Editorial;

use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Livewire\Features\WithPagination;
use Testa\Storefront\Queries\Editorial\GetAuthors;

class AuthorsListPage extends Page
{
    use WithPagination;

    public function render(): View
    {
        $authors = new GetAuthors()->execute();

        return view('testa::storefront.livewire.editorial.authors-list', compact('authors'))
            ->title(__('Autoras'));
    }
}
