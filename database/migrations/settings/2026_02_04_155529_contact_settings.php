<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('contact.email_address', 'info@email.net');
        $this->migrator->add('contact.phone_number', '000 000 000');
        $this->migrator->add('contact.instagram_url');
        $this->migrator->add('contact.facebook_url');
        $this->migrator->add('contact.x_url');
        $this->migrator->add('contact.bluesky_url');
        $this->migrator->add('contact.youtube_url');
        $this->migrator->add('contact.vimeo_url');
        $this->migrator->add('contact.mastodon_url');
        $this->migrator->add('contact.soundcloud_url');
        $this->migrator->add('contact.telegram_url');
        $this->migrator->add('contact.whatsapp_url');
    }
};
