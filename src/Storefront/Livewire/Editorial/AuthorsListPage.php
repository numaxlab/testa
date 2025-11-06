<?php

namespace Trafikrak\Storefront\Livewire\Editorial;

use Illuminate\View\View;
use Livewire\WithPagination;
use NumaxLab\Lunar\Geslib\Models\Author;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;

class AuthorsListPage extends Page
{
    use WithPagination;

    public function render(): View
    {
        $authors = Author::whereHas('products', function ($query) {
            $query->whereHas('brand', function ($query) {
                $query->where('attribute_data->in-house->value', true);
            });
        })
            ->orderBy('name', 'ASC')
            ->with([
                'defaultUrl',
                'media',
            ])
            ->paginate(32);

        return view('trafikrak::storefront.livewire.editorial.authors-list', compact('authors'))
            ->title(__('Autoras'));
    }
}
