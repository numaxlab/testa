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
