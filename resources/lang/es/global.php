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
            'description' => 'Paga con tu tarjeta de crédito o débito de forma segura a través de nuestra plataforma de pago.',
        ],
        'bizum' => [
            'title' => 'Bizum',
            'description' => 'Paga al instante desde tu móvil con Bizum. Recibirás un SMS de confirmación.',
        ],
        'paypal' => [
            'title' => 'PayPal',
            'description' => 'Paga de forma segura a través de tu cuenta de PayPal.',
        ],
        'cash-on-delivery' => [
            'title' => 'Contra Reembolso',
            'description' => 'Paga en efectivo al recibir tu pedido. Disponible solo para envíos dentro de España.',
        ],
        'credit' => [
            'title' => 'A crédito',
            'description' => 'Pago aplazado para cuentas habilitadas. Recibirás la factura en tu correo electrónico.',
        ],
        'transfer' => [
            'title' => 'Transferencia bancaria',
            'description' => 'Realiza una transferencia bancaria tras confirmar el pedido. Te enviaremos los datos de nuestra cuenta.',
        ],
        'direct-debit' => [
            'title' => 'Domiciliación bancaria',
            'description' => 'Autoriza el cargo en tu cuenta bancaria. Necesitarás proporcionar tu IBAN y los datos del titular.',
        ],
    ],
];
