<?php

namespace Testa\Settings;

use Spatie\LaravelSettings\Settings;

class TextSettings extends Settings
{
    public array $membership_intro;

    public array $membership_options_description;

    public array $privacy_policy_text;

    public static function group(): string
    {
        return 'text';
    }

    public function getMembershipIntro(): string
    {
        return $this->membership_intro[app()->getLocale()]
            ?? $this->membership_intro[config('app.fallback_locale')]
            ?? '';
    }

    public function getMembershipOptionsDescription(): string
    {
        return $this->membership_options_description[app()->getLocale()]
            ?? $this->membership_options_description[config('app.fallback_locale')]
            ?? '';
    }

    public function getPrivacyPolicyText(): string
    {
        return $this->privacy_policy_text[app()->getLocale()]
            ?? $this->privacy_policy_text[config('app.fallback_locale')]
            ?? '';
    }
}
