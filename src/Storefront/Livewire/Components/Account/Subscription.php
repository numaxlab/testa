<?php

namespace Testa\Storefront\Livewire\Components\Account;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Testa\Models\Content\Location;
use Testa\Storefront\Queries\Account\GetCustomerActiveSubscriptions;
use Testa\Storefront\Queries\Content\GetBannerByLocation;

class Subscription extends Component
{
    public ?Collection $subscriptions;

    public function mount(): void
    {
        $this->subscriptions = new GetCustomerActiveSubscriptions()->execute(auth()->user()->latestCustomer());
    }

    public function render(): View
    {
        $banner = new GetBannerByLocation()->execute(Location::USER_DASHBOARD_SUBSCRIPTIONS);

        return view('testa::storefront.livewire.components.account.subscription', compact('banner'));
    }
}
