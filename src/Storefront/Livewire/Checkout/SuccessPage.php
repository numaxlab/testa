<?php

namespace Testa\Storefront\Livewire\Checkout;

use Illuminate\View\View;
use Lunar\Models\Order;
use Lunar\Shipping\Models\ShippingMethod;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;

class SuccessPage extends Page
{
    public Order $order;

    public ?string $shippingDescription = null;

    public function mount($fingerprint): void
    {
        $this->order = Order::where('fingerprint', $fingerprint)
            ->with(['shippingAddress'])
            ->firstOrFail();

        $identifier = $this->order->shipping_breakdown->items->first()?->identifier;
        if ($identifier) {
            $this->shippingDescription = ShippingMethod::where('code', $identifier)->value('description');
        }
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.checkout.success')
            ->title(__('Pedido finalizado'));
    }
}