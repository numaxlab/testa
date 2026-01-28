<?php

namespace Testa\Storefront\Livewire\Checkout;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Lunar\Facades\CartSession;
use Lunar\Models\Contracts\Cart;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;

class SummaryPage extends Page
{
    private const int GESLIB_PRODUCT_TYPE_ID = 1;

    public ?Cart $cart;

    public array $lines;

    public function getCartLinesProperty(): Collection
    {
        return $this->cart->lines ?? collect();
    }

    public function rules(): array
    {
        return [
            'lines.*.quantity' => 'required|numeric|min:1|max:100',
        ];
    }

    public function mount(): void
    {
        $this->cart = CartSession::current();

        if (! $this->cart || $this->cart->lines->isEmpty()) {
            $this->redirect('/');

            return;
        }

        $this->removeNonGeslibItems();
        $this->mapLines();
    }

    private function removeNonGeslibItems(): void
    {
        $cartLines = $this->cart->lines()->with('purchasable.product')->get();

        $invalidLineIds = $cartLines->filter(function ($line) {
            if ($line->purchasable_type !== 'product_variant') {
                return true;
            }

            return ! $line->purchasable || $line->purchasable->product?->product_type_id !== self::GESLIB_PRODUCT_TYPE_ID;
        })->pluck('id');

        foreach ($invalidLineIds as $lineId) {
            CartSession::remove($lineId);
        }

        if ($invalidLineIds->isNotEmpty()) {
            $this->cart = CartSession::current();
        }
    }

    private function mapLines(): void
    {
        $this->lines = $this->cartLines
            ->filter(function ($line) {
                return $line->purchasable_type === 'product_variant'
                    && $line->purchasable?->product?->product_type_id === self::GESLIB_PRODUCT_TYPE_ID;
            })
            ->map(function ($line) {
                return [
                    'id' => $line->id,
                    'slug' => $line->purchasable->product->defaultUrl->slug,
                    'quantity' => $line->quantity,
                    'description' => $line->purchasable->getDescription(),
                    'thumbnail' => $line->purchasable->getThumbnailUrl(),
                    'sub_total' => $line->subTotal->formatted(),
                    'unit_price' => $line->unitPriceInclTax->formatted(),
                ];
            })->toArray();

        if (count($this->lines) === 0) {
            CartSession::forget();
        }
    }

    public function updateLines(): void
    {
        $this->validate();

        CartSession::updateLines(collect($this->lines));

        $this->mapLines();

        $this->dispatch('cartUpdated');
    }

    public function removeLine($id): void
    {
        CartSession::remove($id);

        $this->mapLines();
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.checkout.summary');
    }
}
