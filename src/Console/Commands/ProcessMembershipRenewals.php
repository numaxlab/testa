<?php

namespace Testa\Console\Commands;

use Illuminate\Console\Command;
use Testa\Jobs\ProcessMembershipRenewal;
use Testa\Models\Membership\Subscription;

/**
 * Manual / schedulable command to process membership renewals via Redsys MIT.
 *
 * Dispatches jobs for ALL active subscriptions that are due for renewal,
 * including those without a stored payment_identifier. When the payment
 * identifier is absent the Job transitions the subscription to
 * pending_payment so staff can follow up manually.
 *
 * The scheduler wiring (routes/console.php) is NOT activated by this command;
 * it remains behind a feature flag until bank confirmation (Phase 3).
 */
class ProcessMembershipRenewals extends Command
{
    protected $signature = 'testa:process-membership-renewals
                            {--dry-run : List eligible subscriptions without dispatching jobs}';

    protected $description = 'Dispatch renewal jobs for active expired memberships (job handles missing-token fallback)';

    public function handle(): void
    {
        $query = Subscription::query()
            ->where('status', Subscription::STATUS_ACTIVE)
            ->whereDate('expires_at', '<=', now());

        $count = $query->count();

        if ($this->option('dry-run')) {
            $this->info("Dry run: {$count} eligible subscription(s) found.");

            return;
        }

        $dispatched = 0;

        $query->each(function (Subscription $subscription) use (&$dispatched) {
            ProcessMembershipRenewal::dispatch($subscription);
            $dispatched++;
        });

        $this->info("ProcessMembershipRenewals: dispatched {$dispatched} renewal job(s).");
    }
}
