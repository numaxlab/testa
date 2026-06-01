<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Testa\Jobs\ProcessMembershipRenewal;
use Testa\Models\Membership\MembershipPlan;
use Testa\Models\Membership\Subscription;
use Testa\Payment\Adapters\RedsysRecurringAdapter;
use Testa\Payment\RecurringChargeResult;

// Helper: insert a tier + plan with the given billing_interval, return the plan ID.
function createPlanWithInterval(string $billingInterval): int
{
    $tierId = DB::table('membership_tiers')->insertGetId([
        'name'        => json_encode(['es' => 'Tier']),
        'description' => json_encode(['es' => 'Tier desc']),
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);

    return DB::table('membership_plans')->insertGetId([
        'membership_tier_id' => $tierId,
        'name'               => json_encode(['es' => 'Plan']),
        'description'        => json_encode(['es' => 'Desc']),
        'billing_interval'   => $billingInterval,
        'created_at'         => now(),
        'updated_at'         => now(),
    ]);
}

beforeEach(function () {
    Language::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true, 'decimal_places' => 2, 'exchange_rate' => 1]);
});

describe('ProcessMembershipRenewal — expires_at respects billing interval', function () {
    $cases = [
        ['interval' => MembershipPlan::BILLING_INTERVAL_MONTHLY,   'method' => 'addMonth',    'label' => 'monthly'],
        ['interval' => MembershipPlan::BILLING_INTERVAL_QUARTERLY,  'method' => 'addMonths',   'args'  => [3], 'label' => 'quarterly'],
        ['interval' => MembershipPlan::BILLING_INTERVAL_YEARLY,     'method' => 'addYear',     'label' => 'yearly'],
    ];

    foreach ($cases as $case) {
        it("sets expires_at to +1 {$case['label']} on successful renewal", function () use ($case) {
            $now = Carbon::create(2025, 6, 1, 12, 0, 0);
            Carbon::setTestNow($now);

            $planId = createPlanWithInterval($case['interval']);

            $sub = Subscription::factory()->create([
                'membership_plan_id' => $planId,
                'status'             => Subscription::STATUS_ACTIVE,
                'payment_identifier' => 'TOKEN_ABC',
                'started_at'         => now()->subYear(),
                'expires_at'         => now()->subDay(),
            ]);

            // Mock the adapter to return a successful charge
            $this->mock(RedsysRecurringAdapter::class, function ($mock) {
                $mock->shouldReceive('charge')
                    ->once()
                    ->andReturn(new RecurringChargeResult(success: true, aborted: false, errorMessage: null));
            });

            $job = new ProcessMembershipRenewal($sub);
            $job->handle();

            $sub->refresh();

            $expected = isset($case['args'])
                ? $now->{$case['method']}(...$case['args'])
                : $now->{$case['method']}();

            expect($sub->status)->toBe(Subscription::STATUS_ACTIVE);
            expect($sub->expires_at->toDateString())->toBe($expected->toDateString());

            Carbon::setTestNow(null);
        });
    }

    it('does not change expires_at when charge fails', function () {
        $planId = createPlanWithInterval(MembershipPlan::BILLING_INTERVAL_MONTHLY);

        $originalExpiry = now()->subDay();

        $sub = Subscription::factory()->create([
            'membership_plan_id' => $planId,
            'status'             => Subscription::STATUS_ACTIVE,
            'payment_identifier' => 'TOKEN_FAIL',
            'started_at'         => now()->subYear(),
            'expires_at'         => $originalExpiry,
        ]);

        $this->mock(RedsysRecurringAdapter::class, function ($mock) {
            $mock->shouldReceive('charge')
                ->once()
                ->andReturn(new RecurringChargeResult(success: false, aborted: false, errorMessage: 'declined'));
        });

        $job = new ProcessMembershipRenewal($sub);
        $job->handle();

        $sub->refresh();

        expect($sub->status)->toBe(Subscription::STATUS_PENDING_PAYMENT);
        expect($sub->expires_at->toDateString())->toBe($originalExpiry->toDateString());
    });
});
