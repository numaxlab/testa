<?php

return [
    'payment_types' => [
        'store' => [
            'card',
            'bizum',
            'paypal',
            'cash-on-delivery',
            'credit',
        ],
        'education' => [
            'card',
            'transfer',
        ],
        'membership' => [
            'direct-debit',
            'card',
        ],
        'donation' => [
            'card',
            'bizum',
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
];
