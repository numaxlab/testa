<?php

namespace Testa\Storefront\Livewire\Bookshop;

use Illuminate\View\View;
use Livewire\Attributes\Url;
use Lunar\Models\Collection;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Livewire\Features\WithPagination;
use Testa\Storefront\Queries\Bookshop\GetSectionProducts;

class SectionPage extends Page
{
    use WithPagination;

    public Collection $section;

    #[Url]
    public string $q = '';

    #[Url]
    public string $t = '';

    public function mount($slug): void
    {
        $this->fetchUrl(
            slug: $slug,
            type: (new Collection)->getMorphClass(),
            firstOrFail: true,
            eagerLoad: [
                'element.children',
            ],
        );

        $this->section = $this->url->element;
    }

    public function render(): View
    {
        $products = new GetSectionProducts()->execute($this->section, $this->q, $this->t);

        return view('testa::storefront.livewire.bookshop.section', compact('products'))
            ->title($this->section->translateAttribute('name'));
    }

    public function search(): void
    {
        $this->resetPage();
    }
}
