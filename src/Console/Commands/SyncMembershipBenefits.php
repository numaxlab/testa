<?php

namespace Testa\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Testa\Models\Customer;
use Testa\Models\Membership\Benefit;

class SyncMembershipBenefits extends Command
{
    protected $signature = 'testa:sync-membership-benefits {--customer= : Customer ID to sync (omit for all)}';

    protected $description = 'Reconcile membership customer group assignments based on active subscriptions';

    public function handle(): void
    {
        $query = Customer::query();

        if ($customerId = $this->option('customer')) {
            $query->where('id', $customerId);
        }

        $query->each(fn (Customer $customer) => $this->sync($customer));

        $this->info('Membership benefits sync complete.');
    }

    public function sync(Customer $customer): void
    {
        $this->syncActiveSubscriptions($customer);
        $this->revokeExpiredAssignments($customer);
    }

    protected function syncActiveSubscriptions(Customer $customer): void
    {
        $activeSubscriptions = $customer->activeSubscriptions()
            ->with('plan.benefits')
            ->get();

        foreach ($activeSubscriptions as $subscription) {
            foreach ($subscription->plan->benefits as $benefit) {
                if ($benefit->code !== Benefit::CUSTOMER_GROUP || ! $benefit->customer_group_id) {
                    continue;
                }

                $existingAssignment = DB::table('membership_customer_group_assignments')
                    ->where('customer_id', $customer->id)
                    ->where('subscription_id', $subscription->id)
                    ->where('benefit_id', $benefit->id)
                    ->where('customer_group_id', $benefit->customer_group_id)
                    ->whereNull('revoked_at')
                    ->first();

                if (! $existingAssignment) {
                    DB::table('membership_customer_group_assignments')->insert([
                        'customer_id' => $customer->id,
                        'subscription_id' => $subscription->id,
                        'benefit_id' => $benefit->id,
                        'customer_group_id' => $benefit->customer_group_id,
                        'expires_at' => $subscription->expires_at,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $customer->customerGroups()->syncWithoutDetaching([$benefit->customer_group_id]);
                }
            }
        }
    }

    protected function revokeExpiredAssignments(Customer $customer): void
    {
        $managedGroupIds = DB::table('membership_customer_group_assignments')
            ->where('customer_id', $customer->id)
            ->whereNull('revoked_at')
            ->pluck('customer_group_id')
            ->unique();

        foreach ($managedGroupIds as $groupId) {
            $hasActiveAssignment = DB::table('membership_customer_group_assignments')
                ->where('customer_id', $customer->id)
                ->where('customer_group_id', $groupId)
                ->whereNull('revoked_at')
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>=', now());
                })
                ->exists();

            if (! $hasActiveAssignment) {
                // Revoke the assignment record(s)
                DB::table('membership_customer_group_assignments')
                    ->where('customer_id', $customer->id)
                    ->where('customer_group_id', $groupId)
                    ->whereNull('revoked_at')
                    ->update(['revoked_at' => now(), 'updated_at' => now()]);

                // Detach from customer group — but only if no other active membership covers it
                $customer->customerGroups()->detach($groupId);
            }
        }
    }
}
