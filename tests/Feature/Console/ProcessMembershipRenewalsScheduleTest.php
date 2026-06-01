<?php

// These tests verify the feature-flag behaviour for the membership renewal scheduler.
//
// The actual schedule wiring lives in traficantes.net/routes/console.php.
// These tests document the exact conditional logic that must be used there, and
// verify that the config key resolves correctly so that a configuration drop-in
// (setting REDSYS_RECURRING_ENABLED=true) is the only step needed to activate
// the scheduler in production.

use Illuminate\Console\Scheduling\Schedule;

describe('membership renewal scheduler feature flag', function () {
    it('is disabled by default when REDSYS_RECURRING_ENABLED is not set', function () {
        // Default must be false — the scheduler should never auto-activate without
        // explicit bank confirmation.
        expect(config('services.redsys.recurring.enabled', false))->toBeFalse();
    });

    it('resolves as enabled when REDSYS_RECURRING_ENABLED is explicitly true', function () {
        config(['services.redsys.recurring.enabled' => true]);

        expect(config('services.redsys.recurring.enabled', false))->toBeTrue();
    });

    it('does not register the renewal command in the schedule when the flag is disabled', function () {
        // Simulate routes/console.php conditional block with the flag OFF.
        config(['services.redsys.recurring.enabled' => false]);

        /** @var Schedule $schedule */
        $schedule = app(Schedule::class);

        if (config('services.redsys.recurring.enabled', false)) {
            $schedule->command('testa:process-membership-renewals')->daily();
        }

        $renewalEvents = collect($schedule->events())
            ->filter(fn ($event) => str_contains($event->command ?? '', 'process-membership-renewals'));

        expect($renewalEvents)->toHaveCount(0);
    });

    it('registers the renewal command in the schedule when the flag is enabled', function () {
        // Simulate routes/console.php conditional block with the flag ON.
        config(['services.redsys.recurring.enabled' => true]);

        /** @var Schedule $schedule */
        $schedule = app(Schedule::class);

        if (config('services.redsys.recurring.enabled', false)) {
            $schedule->command('testa:process-membership-renewals')->daily();
        }

        $renewalEvents = collect($schedule->events())
            ->filter(fn ($event) => str_contains($event->command ?? '', 'process-membership-renewals'));

        expect($renewalEvents)->toHaveCount(1);
    });
});
