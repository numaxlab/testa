<?php

// Feature tests inherit TestCase + RefreshDatabase from Pest.php's global configuration.

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Testa\Jobs\ProcessMembershipRenewal;
use Testa\Models\Membership\Subscription;

beforeEach(function () {
    Language::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true, 'decimal_places' => 2, 'exchange_rate' => 1]);

    // Insert tier and plan directly to avoid observer machinery (product variants, etc.)
    // The command tests only care about subscription fields, not membership product setup.
    $tierId = DB::table('membership_tiers')->insertGetId([
        'name' => json_encode(['es' => 'Socia']),
        'description' => json_encode(['es' => 'Test tier']),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->planId = DB::table('membership_plans')->insertGetId([
        'membership_tier_id' => $tierId,
        'name' => json_encode(['es' => 'Anual']),
        'description' => json_encode(['es' => 'Annual plan']),
        'billing_interval' => 'yearly',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

describe('ProcessMembershipRenewals command', function () {
    it('dispatches a ProcessMembershipRenewal job for each active subscription with a payment identifier', function () {
        Queue::fake();

        $sub = Subscription::factory()->create([
            'membership_plan_id' => $this->planId,
            'status' => Subscription::STATUS_ACTIVE,
            'payment_identifier' => 'TOKEN_VALID_001',
            'started_at' => now()->subYear(),
            'expires_at' => now()->subDay(), // expired — due for renewal
        ]);

        $this->artisan('testa:process-membership-renewals')
            ->assertExitCode(0);

        Queue::assertPushed(ProcessMembershipRenewal::class, function ($job) use ($sub) {
            return $job->subscription->id === $sub->id;
        });
    });

    it('dispatches a job for active expired subscriptions without a payment identifier (job handles fallback)', function () {
        Queue::fake();

        $sub = Subscription::factory()->create([
            'membership_plan_id' => $this->planId,
            'status' => Subscription::STATUS_ACTIVE,
            'payment_identifier' => null,
            'started_at' => now()->subYear(),
            'expires_at' => now()->subDay(),
        ]);

        $this->artisan('testa:process-membership-renewals')
            ->assertExitCode(0);

        Queue::assertPushed(ProcessMembershipRenewal::class, function ($job) use ($sub) {
            return $job->subscription->id === $sub->id;
        });
    });

    it('does not dispatch jobs for cancelled subscriptions', function () {
        Queue::fake();

        Subscription::factory()->create([
            'membership_plan_id' => $this->planId,
            'status' => Subscription::STATUS_CANCELLED,
            'payment_identifier' => 'TOKEN_VALID_001',
            'started_at' => now()->subYear(),
            'expires_at' => now()->subDay(),
        ]);

        $this->artisan('testa:process-membership-renewals')
            ->assertExitCode(0);

        Queue::assertNothingPushed();
    });

    it('does not dispatch jobs for active subscriptions not yet due for renewal', function () {
        Queue::fake();

        Subscription::factory()->create([
            'membership_plan_id' => $this->planId,
            'status' => Subscription::STATUS_ACTIVE,
            'payment_identifier' => 'TOKEN_VALID_FUTURE',
            'started_at' => now(),
            'expires_at' => now()->addMonth(), // still active, not due
        ]);

        $this->artisan('testa:process-membership-renewals')
            ->assertExitCode(0);

        Queue::assertNothingPushed();
    });

    it('transitions subscription to pending_payment when payment identifier is missing at job level', function () {
        $sub = Subscription::factory()->create([
            'membership_plan_id' => $this->planId,
            'status' => Subscription::STATUS_ACTIVE,
            'payment_identifier' => null,
            'started_at' => now()->subYear(),
            'expires_at' => now()->subDay(),
        ]);

        // Dispatch the job directly (synchronously) to verify safe degradation
        $job = new ProcessMembershipRenewal($sub);
        $job->handle();

        $sub->refresh();
        expect($sub->status)->toBe(Subscription::STATUS_PENDING_PAYMENT);
    });
});
