<?php

return [
    'payment_types' => [
        'store' => [
            'cash-on-delivery',
        ],
        'education' => [
            'transfer',
        ],
        'membership' => [
            'direct-debit',
        ],
        'donation' => [
            'direct-debit',
        ],
    ],

    'default_billing_address' => [
        'country_iso2' => env('DEFAULT_BILLING_ADDRESS_COUNTRY', 'ES'),
        'line_one' => env('DEFAULT_BILLING_ADDRESS_LINE_ONE'),
        'city' => env('DEFAULT_BILLING_ADDRESS_CITY'),
        'postcode' => env('DEFAULT_BILLING_ADDRESS_POSTCODE'),
    ],

    'open_graph' => [
        'fallback_image' => env('OPEN_GRAPH_FALLBACK_IMAGE'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Gateways
    |--------------------------------------------------------------------------
    |
    | Configure payment gateway adapters. Each adapter class must implement
    | the PaymentGatewayAdapter interface. The driver name is used to match the
    | adapter to the payment type's driver configured in lunar.payments.types.
    |
    | To add a custom gateway:
    | 1. Create a class implementing Testa\Contracts\Payment\PaymentGatewayAdapter
    | 2. Add it to this array with any required options
    | 3. Configure the driver in config/lunar.php under payments.types
    |
    */
    'payment_gateways' => [
        \Testa\Payment\Adapters\OfflinePaymentAdapter::class => [],
    ],
];
