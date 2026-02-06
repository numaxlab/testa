<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('payment.store', ['cash-on-delivery']);
        $this->migrator->add('payment.education', ['transfer']);
        $this->migrator->add('payment.membership', ['direct-debit']);
        $this->migrator->add('payment.donation', ['direct-debit']);
    }
};
