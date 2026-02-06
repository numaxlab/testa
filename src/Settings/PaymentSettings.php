<?php

namespace Testa\Settings;

use Spatie\LaravelSettings\Settings;

class PaymentSettings extends Settings
{
    public array $store;

    public array $education;

    public array $membership;

    public array $donation;

    public static function group(): string
    {
        return 'payment';
    }

    public static function getAvailablePaymentTypes(): array
    {
        $types = array_keys(config('lunar.payments.types', []));

        return collect($types)->mapWithKeys(function ($type) {
            return [$type => __("testa::global.payment_types.{$type}.title")];
        })->toArray();
    }
}
