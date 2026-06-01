<?php

namespace Testa\Listeners;

use Illuminate\Support\Facades\Log;
use Testa\Models\Membership\Subscription;

/**
 * Persists the Redsys recurring payment identifier on the subscription
 * that corresponds to the just-paid order.
 *
 * Only the opaque token is stored. PAN and CVV are never passed through.
 */
final class PersistRedsysMerchantIdentifier
{
    public function handle(object $event): void
    {
        $subscription = Subscription::where('order_id', $event->orderId)
            ->whereNull('payment_identifier')
            ->first();

        if ($subscription === null) {
            Log::warning('PersistRedsysMerchantIdentifier: no subscription found for order', [
                'order_id' => $event->orderId,
            ]);

            return;
        }

        $subscription->setPaymentIdentifier($event->merchantIdentifier);
    }
}
