<?php

namespace Testa\Storefront\Livewire\Concerns;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Facades\CartSession;
use Lunar\Models\Contracts\Cart;

abstract class AbstractCart extends Component
{
    use FiltersGeslibProducts;

    public array $lines = [];

    public function getCartProperty(): ?Cart
    {
        return CartSession::current();
    }

    public function getCartLinesProperty(): Collection
    {
        return $this->cart?->lines ?? collect();
    }

    public function rules(): array
    {
        return [
            'lines.*.quantity' => 'required|numeric|min:1|max:100',
        ];
    }

    public function updateLines(): void
    {
        $this->validate();

        CartSession::updateLines(collect($this->lines));

        $this->mapLines();

        $this->dispatch('cartUpdated');
    }

    protected function mapLines(): void
    {
        $this->lines = $this
            ->filterGeslibLines($this->cartLines)
            ->map(function ($line) {
                return [
                    'id' => $line->id,
                    'slug' => $line->purchasable->product->defaultUrl->slug,
                    'quantity' => $line->quantity,
                    'description' => $line->purchasable->getDescription(),
                    'thumbnail' => $line->purchasable->getThumbnailUrl(),
                    'sub_total' => $line->subTotal?->formatted(),
                    'unit_price' => $line->unitPriceInclTax?->formatted(),
                ];
            })->toArray();

        if (count($this->lines) === 0) {
            CartSession::forget();
        }
    }

    public function removeLine($id): void
    {
        CartSession::remove($id);

        $this->mapLines();
    }

    abstract public function render(): View;
}
