<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('text.membership_intro', []);
        $this->migrator->add('text.membership_options_description', []);
        $this->migrator->add('text.privacy_policy_text', []);
    }
};
