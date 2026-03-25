<?php

namespace Testa\Storefront\Livewire\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Livewire\Features\WithPagination;
use Testa\Storefront\Queries\Account\GetCustomerOrders;

class OrdersListPage extends Page
{
    use WithPagination;

    public function render(): View
    {
        $orders = new GetCustomerOrders()->execute(Auth::user()->latestCustomer());

        return view('testa::storefront.livewire.account.orders-list', compact('orders'));
    }
}
