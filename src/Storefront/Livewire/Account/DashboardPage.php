<?php

namespace Testa\Storefront\Livewire\Account;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Lunar\Models\Contracts\Address;
use Lunar\Models\Contracts\Customer;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Storefront\Queries\Account\GetCustomerDefaultAddress;

class DashboardPage extends Page
{
    public ?Authenticatable $user;
    public ?Customer $customer;
    public ?Address $defaultAddress;

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->customer = $this->user?->latestCustomer();
        $this->defaultAddress = new GetCustomerDefaultAddress()->execute($this->customer);
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.account.dashboard');
    }
}