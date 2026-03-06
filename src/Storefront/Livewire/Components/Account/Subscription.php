<?php

namespace Testa\Storefront\Livewire\Components\Account;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Testa\Models\Content\Banner;
use Testa\Models\Content\Location;

class Subscription extends Component
{
    public ?Collection $subscriptions;

    public function mount(): void
    {
        $this->subscriptions = auth()->user()->latestCustomer()->activeSubscriptions;
    }

    public function render(): View
    {
        $banner = Banner::whereJsonContains('locations', Location::USER_DASHBOARD_SUBSCRIPTIONS->value)
            ->where('is_published', true)
            ->with('media')
            ->first();

        return view('testa::storefront.livewire.components.account.subscription', compact('banner'));
    }
}
