<?php

return [
    'label' => 'Sesión',
    'plural_label' => 'Sesiones',
    'pages' => [
        'edit' => [
            'title' => 'Información básica',
        ],
        'instructors' => [
            'label' => 'Instructoras',
            'actions' => [
                'attach' => [
                    'label' => 'Asociar',
                    'form' => [
                        'record_id' => [
                            'label' => 'Autora',
                        ],
                    ],
                    'notification' => [
                        'success' => 'Autora asociada correctamente',
                    ],
                ],
                'detach' => [
                    'notification' => [
                        'success' => 'Autora desasociada correctamente',
                    ],
                ],
            ],
        ],
    ],
    'table' => [
        'course_name' => [
            'label' => 'Curso',
        ],
        'name' => [
            'label' => 'Título',
        ],
        'starts_at' => [
            'label' => 'Fecha y hora',
        ],
        'is_published' => [
            'label' => 'Pública',
        ],
    ],
    'form' => [
        'course_id' => [
            'label' => 'Curso',
        ],
        'name' => [
            'label' => 'Título',
        ],
        'subtitle' => [
            'label' => 'Subtítulo',
        ],
        'description' => [
            'label' => 'Descripción',
        ],
        'starts_at' => [
            'label' => 'Fecha y hora',
        ],
        'delivery_method' => [
            'label' => 'Modalidad',
            'options' => [
                'in_person' => 'Presencial',
                'online' => 'En línea',
                'hybrid' => 'Híbrido',
            ],
        ],
        'venue_id' => [
            'label' => 'Lugar',
        ],
        'is_published' => [
            'label' => 'Pública',
        ],
    ],
];
