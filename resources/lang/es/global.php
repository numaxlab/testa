<?php

return [
    'sections' => [
        'education' => 'Formación',
        'media' => 'Mediateca',
        'membership' => 'Membresía',
        'content' => 'Contenido',
        'news' => 'Actualidad',
    ],
    'relation_managers' => [
        'reviews' => 'Reseñas',
    ],
    'payment_types' => [
        'card' => [
            'title' => 'Tarjeta',
            'description' => 'Texto descriptivo',
        ],
        'bizum' => [
            'title' => 'Bizum',
            'description' => 'Texto descriptivo',
        ],
        'paypal' => [
            'title' => 'PayPal',
            'description' => 'Texto descriptivo',
        ],
        'cash-on-delivery' => [
            'title' => 'Contra Reembolso',
            'description' => 'Texto descriptivo',
        ],
        'credit' => [
            'title' => 'A crédito',
            'description' => 'Texto descriptivo',
        ],
        'transfer' => [
            'title' => 'Transferencia bancaria',
            'description' => 'Texto descriptivo',
        ],
        'direct-debit' => [
            'title' => 'Domiciliación bancaria',
            'description' => 'Autoriza el cargo en tu cuenta bancaria. Necesitarás proporcionar tu IBAN y los datos del titular.',
        ],
    ],
];
