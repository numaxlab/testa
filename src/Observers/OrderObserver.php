<?php

namespace Trafikrak\Observers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Lunar\Models\Customer;
use Lunar\Models\Order;
use Trafikrak\Models\Education\Course;
use Trafikrak\Models\Membership\Benefit;
use Trafikrak\Models\Membership\MembershipPlan;
use Trafikrak\Models\Membership\Subscription;

class OrderObserver
{
    public function updated(Order $order): void
    {
        $validStatuses = ['payment-received', 'dispatched'];

        if (! $order->was_redeemed && $order->isDirty('status') && in_array($order->status, $validStatuses)) {
            $this->activateSubscriptionFor($order);
            $this->activateCourseFor($order);
        }
    }

    protected function activateSubscriptionFor(Order $order): void
    {
        $wasRedeemed = false;

        $customer = $order->user->latestCustomer();

        $existingSubscriptions = Subscription::where('order_id', $order->id)
            ->where('customer_id', $customer->id)
            ->where('status', Subscription::STATUS_ACTIVE)
            ->where('expires_at', '>', now())
            ->get()->keyBy('membership_plan_id');

        foreach ($order->lines as $line) {
            if ($line->purchasable_type !== Relation::getMorphAlias(MembershipPlan::class)) {
                continue;
            }

            $membershipPlan = $line->purchasable;

            $startsAt = now();
            $expiresAt = now()->addYear();

            if ($existingSubscriptions->has($membershipPlan->id)) {
                $startsAt = $existingSubscriptions[$membershipPlan->id]->expires_at->addDay();
                $expiresAt = $existingSubscriptions[$membershipPlan->id]->expires_at->addYear();
            }

            $customer->subscriptions()->create([
                'membership_plan_id' => $membershipPlan->id,
                'order_id' => $order->id,
                'status' => Subscription::STATUS_ACTIVE,
                'started_at' => $startsAt,
                'expires_at' => $expiresAt,
            ]);

            $wasRedeemed = true;

            $this->applyBenefits($customer, $membershipPlan);
            $this->calculateRecurringPayment($membershipPlan);

            // Envía email, notifica, etc.

            break;
        }

        if ($wasRedeemed) {
            $order->updateQuietly([
                'was_redeemed' => true,
            ]);
        }
    }

    protected function applyBenefits(Customer $customer, MembershipPlan $membershipPlan): void
    {
        foreach ($membershipPlan->benefits as $benefit) {
            if ($benefit->code === Benefit::CUSTOMER_GROUP) {
                $customer->customerGroups()->attach($benefit->customer_group_id);
            }
        }
    }

    protected function calculateRecurringPayment(MembershipPlan $plan): void
    {
        //
    }

    protected function activateCourseFor(Order $order): void
    {
        $wasRedeemed = false;

        $customer = $order->user->latestCustomer();

        foreach ($order->lines as $line) {
            if ($line->purchasable_type === 'product_variant') {
                if ($line->purchasable->product->product_type_id === CourseObserver::PRODUCT_TYPE_ID) {
                    $course = Course::where('purchasable_id', $line->purchasable->product_id)->first();

                    if ($course && ! $customer->courses->keyBy('id')->has($course->id)) {
                        $customer->courses()->attach($course);
                        $wasRedeemed = true;
                    }
                }
            }
        }

        if ($wasRedeemed) {
            $order->updateQuietly([
                'was_redeemed' => true,
            ]);
        }
    }
}
