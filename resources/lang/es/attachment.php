<?php

return [
    'label' => 'Recursos multimedia',
    'actions' => [
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
