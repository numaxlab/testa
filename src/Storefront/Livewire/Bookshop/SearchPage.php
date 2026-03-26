<?php

namespace Testa\Storefront\Livewire\Bookshop;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Livewire\Features\WithPagination;
use Testa\Storefront\Queries\Bookshop\GetLanguageCollections;
use Testa\Storefront\Queries\Bookshop\GetStatusCollections;
use Testa\Storefront\Queries\Bookshop\SearchProducts;

class SearchPage extends Page
{
    use WithPagination;

    #[Url]
    public ?string $q;

    public ?string $taxonId = '';

    public ?string $languageId = '';

    public ?string $priceRange = '';

    public ?string $availabilityId = '';

    public Collection $languages;

    public Collection $statuses;

    public array $priceRanges = [
        '0-10' => '0 - 10 €',
        '10-20' => '10 - 20 €',
        '20-30' => '20 - 30 €',
        '30-40' => '30 - 40 €',
        '40-50' => '40 - 50 €',
        '50-1000' => '50 - 1.000 €',
    ];

    public function mount(): void
    {
        $search = trim($this->q);

        if (blank($search)) {
            $this->redirect(route('testa.storefront.bookshop.homepage'));
            return;
        }

        $this->languages = new GetLanguageCollections()->execute();
        $this->statuses = new GetStatusCollections()->execute();
    }

    #[On('taxonomy-selected')]
    public function updateSearch($params): void
    {
        $this->taxonId = $params['id'];

        $this->resetPage();
    }

    public function updated($property): void
    {
        if (in_array($property, ['languageId', 'priceRange', 'availabilityId'])) {
            $this->resetPage();
        }
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.bookshop.search');
    }

    #[Computed]
    public function results(): LengthAwarePaginator
    {
        return new SearchProducts()->execute(
            trim($this->q),
            $this->taxonId,
            $this->languageId,
            $this->priceRange,
            $this->availabilityId,
        );
    }
}
