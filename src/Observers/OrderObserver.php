<?php

namespace Trafikrak\Observers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Lunar\Models\Order;
use Trafikrak\Models\Membership\MembershipPlan;
use Trafikrak\Models\Membership\Subscription;

class OrderObserver
{
    public function updated(Order $order): void
    {
        $paidStatus = 'payment-received';

        if ($order->isDirty('status') && $order->status === $paidStatus) {
            $this->activateSubscriptionFor($order);
        }
    }

    protected function activateSubscriptionFor(Order $order): void
    {
        foreach ($order->lines as $line) {
            if ($line->purchasable_type !== Relation::getMorphAlias(MembershipPlan::class)) {
                continue;
            }

            $membershipPlan = $line->purchasable;
            $customer = $order->user->latestCustomer();

            $customer->subscriptions()->create([
                'membership_plan_id' => $membershipPlan->id,
                'status' => Subscription::STATUS_ACTIVE,
                'started_at' => now(),
                'expires_at' => now()->addYear(),
            ]);

            $this->calculateRecurringPayment($membershipPlan);

            // Envía email, notifica, etc.

            break;
        }
    }

    protected function calculateRecurringPayment(MembershipPlan $plan): void
    {
        //
    }
}
