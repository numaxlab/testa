<?php

namespace Testa\Storefront\Livewire\Components\Bookshop;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Product;

class ProductAssociations extends Component
{
    private const int LIMIT = 6;

    public Product $product;

    public bool $isEditorialProduct = false;

    public function render(): View
    {
        return view('testa::storefront.livewire.components.bookshop.product-associations');
    }

    #[Computed]
    public function automaticAssociations(): Collection
    {
        if ($this->manualAssociations()->isNotEmpty() && $this->manualAssociations()->count() >= self::LIMIT) {
            return new Collection();
        }

        $remainingLimit = self::LIMIT - $this->manualAssociations()->count();

        $associations = new Collection();

        $relationships = [
            'authors',
            'editorialCollections',
            'taxonomies',
        ];

        if ($this->isEditorialProduct) {
            $relationships = [
                'editorialCollections',
                'authors',
            ];
        }

        foreach ($relationships as $relationship) {
            if ($associations->count() >= $remainingLimit) {
                break;
            }

            $results = $this->getAssociationsByRelationship(
                relationship: $relationship,
                limit: $remainingLimit - $associations->count(),
                excludeIds: $associations->pluck('id'),
            );

            $associations = $associations->merge($results);
        }

        return $associations;
    }

    #[Computed]
    public function manualAssociations(): Collection
    {
        return $this->product->associations;
    }

    private function getAssociationsByRelationship(string $relationship, int $limit, Collection $excludeIds): Collection
    {
        $relatedIds = $this->product->$relationship->pluck('id');

        if ($relatedIds->isEmpty()) {
            return new Collection();
        }

        return $this
            ->getBaseQuery()
            ->whereHas($relationship, function ($query) use ($relatedIds) {
                $query->whereKey($relatedIds);
            })
            ->whereNotIn('id', $excludeIds)
            ->take($limit)
            ->get();
    }

    private function getBaseQuery(): Builder
    {
        return Product::channel(StorefrontSession::getChannel())
            ->customerGroup(StorefrontSession::getCustomerGroups())
            ->status('published')
            ->whereHas('productType', function ($query) {
                $query->where('id', config('lunar.geslib.product_type_id'));
            })
            ->where('id', '!=', $this->product->id)
            ->with([
                'variant',
                'variant.prices',
                'variant.prices.priceable',
                'variant.prices.priceable.taxClass',
                'variant.prices.priceable.taxClass.taxRateAmounts',
                'variant.prices.currency',
                'media',
                'defaultUrl',
                'authors',
            ]);
    }
}
