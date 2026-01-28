<?php

namespace Testa\Observers;

use Illuminate\Support\Facades\DB;
use Lunar\Models\Customer;
use Lunar\Models\Order;
use Testa\Models\Education\Course;
use Testa\Models\Membership\Benefit;
use Testa\Models\Membership\MembershipPlan;
use Testa\Models\Membership\Subscription;

class OrderObserver
{
    public function updated(Order $order): void
    {
        $validStatuses = ['payment-received', 'dispatched'];

        if (! $order->was_redeemed && $order->isDirty('status') && in_array($order->status, $validStatuses)) {
            DB::transaction(function () use ($order) {
                $this->activateSubscriptionFor($order);
                $this->activateCourseFor($order);
            });
        }
    }

    protected function activateSubscriptionFor(Order $order): void
    {
        $wasRedeemed = false;

        $customer = $order->user->latestCustomer();

        foreach ($order->lines as $line) {
            if ($line->purchasable_type !== 'product_variant') {
                continue;
            }

            if ($line->purchasable->product->product_type_id !== MembershipTierObserver::PRODUCT_TYPE_ID) {
                continue;
            }

            $membershipPlan = MembershipPlan::where('variant_id', $line->purchasable_id)->first();

            if (! $membershipPlan) {
                continue;
            }

            $existingSubscription = Subscription::where('customer_id', $customer->id)
                ->where('membership_plan_id', $membershipPlan->id)
                ->where('status', Subscription::STATUS_ACTIVE)
                ->where('expires_at', '>', now())
                ->first();

            $startsAt = now();
            $expiresAt = now()->addYear();

            if ($existingSubscription) {
                $startsAt = $existingSubscription->expires_at->addDay();
                $expiresAt = $existingSubscription->expires_at->addYear();
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
