<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->delete('payment.education');
        $this->migrator->delete('payment.membership');
    }
};
