<?php

namespace Trafikrak\Storefront\Livewire\Components\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Order;

class LatestOrder extends Component
{
    public ?Order $order = null;

    public bool $hasMoreOrders = false;

    public function mount(): void
    {
        $latestOrders = Auth::user()
            ->latestCustomer()
            ->orders()
            ->where('is_geslib', true)
            ->whereNotIn('status', ['awaiting-payment', 'cancelled'])
            ->latest()
            ->take(2)
            ->get();

        if ($latestOrders->isNotEmpty()) {
            $this->order = $latestOrders->first();
            $this->order->load('productLines.purchasable.product');
        }

        if ($latestOrders->count() > 1) {
            $this->hasMoreOrders = true;
        }
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.components.account.latest-order');
    }
}
