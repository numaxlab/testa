<?php

return [
    'label' => 'Curso',
    'plural_label' => 'Cursos',
    'widgets' => [
        'course-variants' => [
            'save-variants' => 'Guardar variantes',
        ],
    ],
    'pages' => [
        'edit' => [
            'title' => 'Información básica',
        ],
        'products' => [
            'label' => 'Productos',
            'actions' => [
                'attach' => [
                    'label' => 'Asociar',
                    'form' => [
                        'record_id' => [
                            'label' => 'Producto',
                        ],
                    ],
                    'notification' => [
                        'success' => 'Producto asociado correctamente',
                    ],
                ],
                'detach' => [
                    'notification' => [
                        'success' => 'Producto desasociado correctamente',
                    ],
                ],
            ],
        ],
    ],
    'table' => [
        'name' => [
            'label' => 'Título',
        ],
        'is_published' => [
            'label' => 'Público',
        ],
    ],
    'form' => [
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
            'label' => 'Fecha de inicio',
        ],
        'ends_at' => [
            'label' => 'Fecha de fin',
        ],
        'topic_id' => [
            'label' => 'Tema',
        ],
        'is_published' => [
            'label' => 'Público',
        ],
    ],
];
