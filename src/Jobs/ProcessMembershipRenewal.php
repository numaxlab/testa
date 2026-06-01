<?php

namespace Testa\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Testa\Models\Membership\Subscription;
use Testa\Payment\Adapters\RedsysRecurringAdapter;
use Testa\Payment\RedsysRecurringChargeData;

/**
 * Processes a single membership renewal via Redsys MIT.
 *
 * Safe degradation: if the subscription has no payment_identifier the
 * subscription is transitioned to STATUS_PENDING_PAYMENT without any
 * bank call, so manual staff intervention can follow.
 */
class ProcessMembershipRenewal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Subscription $subscription) {}

    public function handle(): void
    {
        if (! $this->subscription->hasPaymentIdentifier()) {
            Log::warning('ProcessMembershipRenewal: subscription has no payment identifier, marking as pending_payment', [
                'subscription_id' => $this->subscription->id,
            ]);

            $this->subscription->update(['status' => Subscription::STATUS_PENDING_PAYMENT]);

            return;
        }

        $adapter = app(RedsysRecurringAdapter::class);

        $amount = $this->resolveAmount();

        $data = new RedsysRecurringChargeData(
            subscription: $this->subscription,
            paymentIdentifier: $this->subscription->payment_identifier,
            configKey: 'redsys_recurring',
            amount: $amount,
        );

        $result = $adapter->charge($data);

        if ($result->aborted) {
            Log::warning('ProcessMembershipRenewal: charge aborted (config missing), subscription unchanged', [
                'subscription_id' => $this->subscription->id,
            ]);

            return;
        }

        if ($result->success) {
            $this->subscription->update([
                'status' => Subscription::STATUS_ACTIVE,
                'started_at' => now(),
                'expires_at' => $this->subscription->plan->nextExpiresAt(),
            ]);

            Log::info('ProcessMembershipRenewal: renewal successful', [
                'subscription_id' => $this->subscription->id,
            ]);
        } else {
            $this->subscription->update(['status' => Subscription::STATUS_PENDING_PAYMENT]);

            Log::warning('ProcessMembershipRenewal: charge failed, subscription marked as pending_payment', [
                'subscription_id' => $this->subscription->id,
                'error' => $result->errorMessage,
            ]);
        }
    }

    protected function resolveAmount(): int
    {
        // Amount in cents sourced from the plan's linked ProductVariant base price.
        // Returns 0 if no plan/variant/price is configured — a zero-amount will be
        // rejected by Redsys, making misconfiguration visible rather than silent.
        return $this->subscription->plan?->priceCents() ?? 0;
    }
}
