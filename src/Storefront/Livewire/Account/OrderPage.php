<?php

namespace Testa\Storefront\Livewire\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Lunar\Models\Order;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Livewire\Features\WithPagination;
use Testa\Storefront\Queries\Account\GetCustomerOrder;

class OrderPage extends Page
{
    use WithPagination;

    public Order $order;

    public function mount($reference): void
    {
        $this->order = new GetCustomerOrder()->execute(
            Auth::user()->latestCustomer(), $reference,
        );
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.account.order');
    }
}
