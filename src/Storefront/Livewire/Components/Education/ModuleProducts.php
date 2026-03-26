<?php

namespace Testa\Storefront\Livewire\Components\Education;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Testa\Models\Education\CourseModule;
use Testa\Storefront\Queries\Education\GetModuleProducts;

class ModuleProducts extends Component
{
    public CourseModule $module;

    public Collection $products;

    public function mount(): void
    {
        $this->products = new GetModuleProducts()->execute($this->module);
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.education.module-products');
    }
}
