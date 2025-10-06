<?php

namespace Trafikrak\Storefront\Livewire\Components\Account;

use Illuminate\View\View;
use Livewire\Component;
use Trafikrak\Models\Membership\Subscription as SubscriptionModel;

class Subscription extends Component
{
    public ?SubscriptionModel $subscription = null;

    public function mount(): void
    {
        $this->subscription = auth()->user()->latestCustomer()->activeSubscription();
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.components.account.subscription');
    }
}
