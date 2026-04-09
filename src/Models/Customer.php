<?php

namespace Testa\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Testa\Models\Education\Course;
use Testa\Models\Membership\Benefit;
use Testa\Models\Membership\Subscription;

class Customer extends \Lunar\Models\Customer
{
    public function canBuyOnCredit(): bool
    {
        return $this->activeSubscriptions()
            ->whereHas('plan.benefits', fn($q) => $q->where('code', Benefit::CREDIT_PAYMENT_TYPE))
            ->exists();
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
            'course_' . config('lunar.database.table_prefix') . 'customer',
        )->withTimestamps()->orderByPivot('created_at', 'desc');
    }
}
