<?php

namespace Testa\Storefront\Livewire\Components\Tier;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Testa\Models\Content\Tier;
use Testa\Storefront\Queries\ProductQueryBuilder;

class EditorialLatest extends Component
{
    public Tier $tier;

    public Collection $products;

    public function mount(): void
    {
        // A paginator cannot be a Livewire property: it defines no synthesizer, so it is
        // resolved here as the plain collection the tier view iterates.
        $this->products = ProductQueryBuilder::build()
            ->whereHas('brand', function ($query) {
                $query->where('attribute_data->in-house->value', true);
            })
            ->orderByDesc('created_at')
            ->limit(12)
            ->get();
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.tier.editorial-latest');
    }
}
