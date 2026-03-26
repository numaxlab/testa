<?php

namespace Testa\Storefront\Livewire\Components\Account;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Testa\Storefront\Queries\Account\GetCustomerAddress;
use Testa\Storefront\Queries\Account\GetCustomerAddresses;
use Testa\Storefront\UseCases\Account\DeleteCustomerAddress;

class Addresses extends Component
{
    public Collection $addresses;

    public function mount(): void
    {
        $customer = Auth::user()?->latestCustomer();
        $this->addresses = new GetCustomerAddresses()->execute($customer);
    }

    public function deleteAddress(int $id): void
    {
        $customer = Auth::user()?->latestCustomer();
        $address = new GetCustomerAddress()->execute($customer, $id);
        new DeleteCustomerAddress()->execute($customer, $address);
        $this->addresses = new GetCustomerAddresses()->execute($customer);
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.account.addresses');
    }
}
