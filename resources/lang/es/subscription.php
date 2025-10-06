<?php

return [
    'label' => 'Subscripción',
    'plural_label' => 'Subscripciones',
    'pages' => [
        'edit' => [
            'title' => 'Información básica',
        ],
    ],
    'table' => [
        'plan' => [
            'label' => 'Plan',
        ],
        'status' => [
            'label' => 'Estado',
            'options' => [
                'active' => 'Activa',
                'cancelled' => 'Cancelada',
            ],
        ],
        'started_at' => [
            'label' => 'Iniciada el',
        ],
        'expires_at' => [
            'label' => 'Expira el',
        ],
    ],
    'form' => [
        'tier_id' => [
            'label' => 'Modalidad',
        ],
        'name' => [
            'label' => 'Nombre',
        ],
        'description' => [
            'label' => 'Descripción',
        ],
        'is_published' => [
            'label' => 'Pública',
        ],
    ],
];
