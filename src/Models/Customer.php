<?php

namespace Trafikrak\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Trafikrak\Models\Membership\Subscription;

class Customer extends \Lunar\Models\Customer
{
    public function activeSubscription(): ?Subscription
    {
        return $this
            ->subscriptions()
            ->where('status', Subscription::STATUS_ACTIVE)
            ->where('expires_at', '>=', now())
            ->first();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
