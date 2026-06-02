<?php

namespace Testa\Settings;

use Spatie\LaravelSettings\Settings;

class EmailSettings extends Settings
{
    public array $order_confirmation_greeting;

    public array $order_confirmation_intro;

    public array $order_pending_payment_greeting;

    public array $order_pending_payment_intro;

    public static function group(): string
    {
        return 'email';
    }

    public function getOrderConfirmationGreeting(): string
    {
        return $this->order_confirmation_greeting[app()->getLocale()]
            ?? $this->order_confirmation_greeting[config('app.fallback_locale')]
            ?? __('testa::mail.order_confirmation.greeting');
    }

    public function getOrderConfirmationIntro(): string
    {
        return $this->order_confirmation_intro[app()->getLocale()]
            ?? $this->order_confirmation_intro[config('app.fallback_locale')]
            ?? '';
    }

    public function getOrderPendingPaymentGreeting(): string
    {
        return $this->order_pending_payment_greeting[app()->getLocale()]
            ?? $this->order_pending_payment_greeting[config('app.fallback_locale')]
            ?? __('testa::mail.order_pending_payment.greeting');
    }

    public function getOrderPendingPaymentIntro(): string
    {
        return $this->order_pending_payment_intro[app()->getLocale()]
            ?? $this->order_pending_payment_intro[config('app.fallback_locale')]
            ?? '';
    }
}
