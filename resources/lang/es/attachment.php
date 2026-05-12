<?php

return [
    'label' => 'Recursos multimedia',
    'actions' => [
        'attach_to' => [
            'label' => 'Asociar a...',
            'form' => [
                'attachable' => [
                    'label' => 'Elemento',
                ],
            ],
            'notification' => [
                'success' => 'Recurso multimedia asociado correctamente',
            ],
        ],
        'attach' => [
            'label' => 'Asociar',
            'form' => [
                'media' => [
                    'label' => 'Recurso multimedia',
                ],
            ],
            'notification' => [
                'success' => 'Recurso multimedia asociado correctamente',
            ],
        ],
        'detach' => [
            'notification' => [
                'success' => 'Recurso multimedia desasociado correctamente',
            ],
        ],
    ],
    'table' => [
        'attachable' => [
            'label' => 'Elemento',
        ],
        'attachable_type' => [
            'label' => 'Tipo',
        ],
        'type' => [
            'label' => 'Tipo',
            'options' => [
                'audio' => 'Audio',
                'video' => 'Video',
                'document' => 'Documento',
                'unknown' => 'Desconocido',
            ],
        ],
        'name' => [
            'label' => 'Nombre',
        ],
    ],
];
