<?php

namespace Testa\Storefront\Livewire\Account;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Lunar\Models\Contracts\Address;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Storefront\Queries\Account\GetCustomerDefaultAddress;

class DashboardPage extends Page
{
    public ?Authenticatable $user;
    public ?Address $defaultAddress;

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->defaultAddress = new GetCustomerDefaultAddress()->execute($this->user?->latestCustomer());
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.account.dashboard');
    }
}