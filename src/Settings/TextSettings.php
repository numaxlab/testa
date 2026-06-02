<?php

namespace Testa\Settings;

use Spatie\LaravelSettings\Settings;

class TextSettings extends Settings
{
    public array $membership_intro;

    public array $membership_options_description;

    public array $privacy_policy_text;

    public array $itineraries_intro;

    public array $donate_intro;

    public array $shipping_home_description;

    public array $checkout_success_text;

    public array $course_register_success_text;

    public array $donate_success_text;

    public array $signup_success_text;

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

    public function getItinerariesIntro(): string
    {
        return $this->itineraries_intro[app()->getLocale()]
            ?? $this->itineraries_intro[config('app.fallback_locale')]
            ?? '';
    }

    public function getDonateIntro(): string
    {
        return $this->donate_intro[app()->getLocale()]
            ?? $this->donate_intro[config('app.fallback_locale')]
            ?? '';
    }

    public function getShippingHomeDescription(): string
    {
        return $this->shipping_home_description[app()->getLocale()]
            ?? $this->shipping_home_description[config('app.fallback_locale')]
            ?? '';
    }

    public function getSignupSuccessText(): string
    {
        return $this->signup_success_text[app()->getLocale()]
            ?? $this->signup_success_text[config('app.fallback_locale')]
            ?? '';
    }

    public function getDonateSuccessText(): string
    {
        return $this->donate_success_text[app()->getLocale()]
            ?? $this->donate_success_text[config('app.fallback_locale')]
            ?? '';
    }

    public function getCourseRegisterSuccessText(): string
    {
        return $this->course_register_success_text[app()->getLocale()]
            ?? $this->course_register_success_text[config('app.fallback_locale')]
            ?? '';
    }

    public function getCheckoutSuccessText(): string
    {
        return $this->checkout_success_text[app()->getLocale()]
            ?? $this->checkout_success_text[config('app.fallback_locale')]
            ?? '';
    }
}
