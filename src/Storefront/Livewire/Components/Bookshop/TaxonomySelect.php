<?php

namespace Testa\Storefront\Livewire\Components\Bookshop;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Collection;
use NumaxLab\Lunar\Geslib\Handle;

class TaxonomySelect extends Component
{
    public $search = '';
    public $selectedId = null;
    public $selectedName = 'Selecciona una materia';
    public $isOpen = false;

    public function selectOption($id, $name): void
    {
        $this->selectedId = $id;
        $this->selectedName = $name;
        $this->isOpen = false;
        $this->search = '';

        $this->dispatch('taxonomy-selected', ['id' => $id]);
    }

    public function clearSelection(): void
    {
        $this->selectedId = null;
        $this->selectedName = 'Selecciona una materia';
        $this->isOpen = false;
        $this->search = '';

        $this->dispatch('taxonomy-selected', ['id' => null]);
    }

    public function render(): View
    {
        $options = Collection::search($this->search)
            ->query(function (Builder $query) {
                $query
                    ->whereHas('group', function ($query) {
                        $query->where('handle', Handle::COLLECTION_GROUP_TAXONOMIES);
                    })->channel(StorefrontSession::getChannel())
                    ->customerGroup(StorefrontSession::getCustomerGroups());
            })->get();

        return view('testa::storefront.livewire.components.bookshop.taxonomy-select', [
            'options' => $options,
        ]);
    }
}
