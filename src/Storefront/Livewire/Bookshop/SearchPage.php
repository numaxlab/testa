<?php

namespace Testa\Storefront\Livewire\Bookshop;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Collection as LunarCollection;
use Lunar\Models\Product;
use Meilisearch\Endpoints\Indexes;
use NumaxLab\Lunar\Geslib\InterCommands\LanguageCommand;
use NumaxLab\Lunar\Geslib\InterCommands\StatusCommand;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Livewire\Features\WithPagination;

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

        $this->languages = LunarCollection::whereHas('group', function ($query) {
            $query->where('handle', LanguageCommand::HANDLE);
        })->channel(StorefrontSession::getChannel())
            ->customerGroup(StorefrontSession::getCustomerGroups())
            ->get();
        $this->statuses = LunarCollection::whereHas('group', function ($query) {
            $query->where('handle', StatusCommand::HANDLE);
        })->channel(StorefrontSession::getChannel())
            ->customerGroup(StorefrontSession::getCustomerGroups())
            ->get();
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
        $filters = $this->buildFilters();

        return Product::search(
            trim($this->q),
            function (Indexes $search, string $query, array $options) use ($filters) {
                if (! empty($filters)) {
                    $options['filter'] = $filters;
                }

                return $search->search($query, $options);
            })
            ->query(fn(Builder $query) => $query->with([
                'variant',
                'variant.taxClass',
                'defaultUrl',
                'urls',
                'thumbnail',
                'authors',
                'prices',
            ]))
            ->paginate(18);
    }

    private function buildFilters(): string
    {
        $filters = [];

        if ($this->taxonId) {
            $filters[] = "taxonomies.id IN [$this->taxonId]";
        }

        if ($this->languageId) {
            $filters[] = "languages.id IN [$this->languageId]";
        }

        if ($this->priceRange) {
            [$min, $max] = explode('-', $this->priceRange);
            $filters[] = "price >= $min AND price <= $max";
        }

        if ($this->availabilityId) {
            $filters[] = "geslib_status.id = $this->availabilityId";
        }

        return implode(' AND ', $filters);
    }
}
