<?php

namespace Testa\Storefront\Livewire\Components\Bookshop;

use Illuminate\View\View;
use Livewire\Component;
use Testa\Storefront\Queries\Bookshop\SearchTaxonomies;

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
        $options = new SearchTaxonomies()->execute($this->search);

        return view('testa::storefront.livewire.components.bookshop.taxonomy-select', [
            'options' => $options,
        ]);
    }
}
