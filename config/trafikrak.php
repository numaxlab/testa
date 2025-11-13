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
            'direct-debit',
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
];
