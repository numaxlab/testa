<?php

namespace Testa\Storefront\Livewire\Editorial;

use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Models\Author;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Livewire\Features\WithPagination;
use Testa\Storefront\Queries\Editorial\GetAuthorProducts;

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
        $products = new GetAuthorProducts()->execute($this->author, 18);

        return view('testa::storefront.livewire.editorial.author', compact('products'))
            ->title($this->author->name);
    }
}
