<?php

namespace Testa\Storefront\Livewire\Membership;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Lunar\Models\Order;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Settings\TextSettings;
use Testa\Storefront\Queries\Checkout\GetPlacedOrderById;

class DonateSuccessPage extends Page
{
    public Order $order;

    public function mount($id, $fingerprint): void
    {
        $this->order = new GetPlacedOrderById()->execute($id, $fingerprint);

        if (Auth::id() !== $this->order->user_id) {
            abort(403);
        }
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.membership.donate-success', [
            'successText' => app(TextSettings::class)->getDonateSuccessText(),
        ])->title(__('Gracias por tu donación'));
    }
}
