<?php

namespace Testa\Settings;

use Spatie\LaravelSettings\Settings;

class ContactSettings extends Settings
{
    public string $email_address;
    public string $phone_number;
    public ?array $address;
    public ?string $instagram_url;
    public ?string $facebook_url;
    public ?string $x_url;
    public ?string $bluesky_url;
    public ?string $mastodon_url;
    public ?string $youtube_url;
    public ?string $vimeo_url;
    public ?string $soundcloud_url;
    public ?string $telegram_url;
    public ?string $whatsapp_url;

    public static function group(): string
    {
        return 'contact';
    }

    public function getPrimaryAddress(): ?array
    {
        foreach ($this->address as $address) {
            if ($address['is_primary'] === true) {
                return $address;
            }
        }

        return null;
    }
}