<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('email.order_confirmation_greeting', []);
        $this->migrator->add('email.order_confirmation_intro', []);
        $this->migrator->add('email.order_pending_payment_greeting', []);
        $this->migrator->add('email.order_pending_payment_intro', []);
    }
};
