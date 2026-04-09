<?php

namespace Testa\Storefront\Livewire\Checkout;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Lunar\Models\Order;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Storefront\Queries\Checkout\GetOrderById;
use Testa\Storefront\Queries\Checkout\GetShippingMethodDescription;

class SuccessPage extends Page
{
    public Order $order;

    public ?string $shippingDescription = null;

    public function mount($id, $fingerprint): void
    {
        $this->order = new GetOrderById()->execute($id, $fingerprint);

        if (Auth::id() !== $this->order->user_id) {
            abort(403);
        }

        $identifier = $this->order->shipping_breakdown->items->first()?->identifier;
        if ($identifier) {
            $this->shippingDescription = new GetShippingMethodDescription()->execute($identifier);
        }
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.checkout.success')
            ->title(__('Pedido finalizado'));
    }
}