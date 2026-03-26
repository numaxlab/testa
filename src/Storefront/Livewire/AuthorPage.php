<?php

namespace Testa\Storefront\Livewire;

use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Models\Author;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Livewire\Features\WithPagination;
use Testa\Storefront\Queries\Editorial\GetAuthorProducts;
use Testa\Storefront\Queries\Education\CheckAuthorHasMedia;

class AuthorPage extends Page
{
    use WithPagination;

    public Author $author;

    public function mount($slug): void
    {
        $this->fetchUrl(
            slug: $slug,
            type: (new Author)->getMorphClass(),
            firstOrFail: true,
        );

        $this->author = $this->url->element;
    }

    public function render(): View
    {
        $products = new GetAuthorProducts()->execute($this->author);
        $hasMedia = new CheckAuthorHasMedia()->execute($this->author);

        return view('testa::storefront.livewire.author', compact('products', 'hasMedia'))
            ->title($this->author->name);
    }
}
