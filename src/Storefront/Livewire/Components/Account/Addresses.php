<?php

namespace Testa\Storefront\Livewire\Components\Account;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class Addresses extends Component
{
    public Collection $addresses;

    public function mount(): void
    {
        $this->addresses = Auth::user()?->latestCustomer()->addresses;
    }

    public function deleteAddress(int $id): void
    {
        $customer = Auth::user()?->latestCustomer();
        $address = $customer->addresses()->findOrFail($id);
        $address->delete();
        $this->addresses = $customer->addresses()->get();
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.account.addresses');
    }
}
