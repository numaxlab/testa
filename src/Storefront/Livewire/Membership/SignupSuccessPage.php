<?php

namespace Trafikrak\Storefront\Livewire\Membership;

use Illuminate\View\View;
use Lunar\Models\Order;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;

class SignupSuccessPage extends Page
{
    public Order $order;

    public function mount($fingerprint): void
    {
        $this->order = Order::where('fingerprint', $fingerprint)
            ->whereNotNull('placed_at')
            ->firstOrFail();
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.membership.signup-success')
            ->title(__('Ya eres socix'));
    }
}
