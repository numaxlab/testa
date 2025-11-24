<?php

return [
    'label' => 'Vídeo',
    'plural_label' => 'Vídeos',
    'pages' => [
        'edit' => [
            'title' => 'Información básica',
        ],
    ],
    'table' => [
        'name' => [
            'label' => 'Nombre',
        ],
        'is_published' => [
            'label' => 'Público',
        ],
    ],
    'form' => [
        'name' => [
            'label' => 'Nombre',
        ],
        'description' => [
            'label' => 'Descripción',
        ],
        'source' => [
            'label' => 'Fuente',
            'options' => [
                'youtube' => 'YouTube',
                'vimeo' => 'Vimeo',
            ],
        ],
        'source_id' => [
            'label' => 'Código embebido',
        ],
        'visibility' => [
            'label' => 'Visibilidad',
            'options' => [
                'public' => 'Público',
                'private' => 'Privado',
            ],
        ],
        'is_published' => [
            'label' => 'Público',
        ],
    ],
];
