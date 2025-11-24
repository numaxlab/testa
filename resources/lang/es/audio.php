<?php

return [
    'label' => 'Audio',
    'plural_label' => 'Audios',
    'pages' => [
        'edit' => [
            'title' => 'Información básica',
        ],
        'attachments' => [
            'label' => 'Elementos relacionados',
            'actions' => [
                'attach' => [
                    'label' => 'Asociar',
                    'form' => [
                        'record_id' => [
                            'label' => 'Producto/curso',
                        ],
                    ],
                    'notificaton' => [
                        'success' => 'Registro asociado correctamente',
                    ],
                ],
                'detach' => [
                    'notificaton' => [
                        'success' => 'Registro desasociado correctamente',
                    ],
                ],
            ],
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
                'soundcloud' => 'SoundCloud',
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
