<?php

namespace Testa\Storefront\Livewire\Components\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Order;
use Testa\Storefront\Queries\Account\GetCustomerLatestOrders;

class LatestOrder extends Component
{
    public ?Order $order = null;

    public bool $hasMoreOrders = false;

    public function mount(): void
    {
        $latestOrders = new GetCustomerLatestOrders()->execute(Auth::user()->latestCustomer());

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
        return view('testa::storefront.livewire.components.account.latest-order');
    }
}
