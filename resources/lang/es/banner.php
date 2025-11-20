<?php

return [
    'label' => 'Banner',
    'plural_label' => 'Banners',
    'pages' => [
        'edit' => [
            'title' => 'Información básica',
        ],
    ],
    'table' => [
        'name' => [
            'label' => 'Nombre',
        ],
        'type' => [
            'label' => 'Tipo',
        ],
        'is_published' => [
            'label' => 'Público',
        ],
    ],
    'form' => [
        'name' => [
            'label' => 'Nombre',
        ],
        'type' => [
            'label' => 'Tipo',
            'options' => [
                'full_width' => 'Ancho completo',
                'contained' => 'Contenido',
            ],
        ],
        'locations' => [
            'label' => 'Ubicaciones',
            'options' => [
                'user_dashboard_subscriptions' => 'Panel de usuario - Subscripciones',
                'course' => 'Página de curso',
                'course_register' => 'Página de inscripción en un curso',
            ],
        ],
        'description' => [
            'label' => 'Descripción',
        ],
        'link' => [
            'label' => 'Enlace',
        ],
        'button_text' => [
            'label' => 'Texto del botón',
        ],
        'is_published' => [
            'label' => 'Público',
        ],
    ],
];
