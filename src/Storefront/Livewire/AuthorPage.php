<?php

namespace Testa\Storefront\Livewire;

use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Models\Author;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Livewire\Features\WithPagination;
use Testa\Models\Education\CourseModule;
use Testa\Storefront\Queries\ProductQueryBuilder;

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
        $products = ProductQueryBuilder::build()
            ->whereHas('authors', function ($query) {
                $query->where((new Author)->getTable().'.id', $this->author->id);
            })
            ->paginate(12);

        $hasMedia = CourseModule::whereHas('instructors', function ($query) {
            $query->where((new Author)->getTable().'.id', $this->author->id);
        })->where('is_published', true)->exists();

        return view('testa::storefront.livewire.author', compact('products', 'hasMedia'))
            ->title($this->author->name);
    }
}
