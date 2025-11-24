<?php

namespace Trafikrak\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Trafikrak\Models\Education\Course;
use Trafikrak\Models\Membership\Benefit;
use Trafikrak\Models\Membership\Subscription;

class Customer extends \Lunar\Models\Customer
{
    public function canBuyOnCredit(): bool
    {
        foreach ($this->activeSubscriptions as $activeSubscription) {
            foreach ($activeSubscription->plan->benefits as $benefit) {
                if ($benefit->code === Benefit::CREDIT_PAYMENT_TYPE) {
                    return true;
                }
            }
        }

        return false;
    }

    public function activeSubscriptions(): HasMany
    {
        return $this
            ->subscriptions()
            ->where('status', Subscription::STATUS_ACTIVE)
            ->where('expires_at', '>=', now())
            ->orderBy('started_at');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'course_'.config('lunar.database.table_prefix').'customer',
        )->withTimestamps()->orderByPivot('created_at', 'desc');
    }
}
