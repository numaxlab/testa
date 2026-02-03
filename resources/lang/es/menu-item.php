<?php

return [
    'label' => 'elemento de menú',
    'plural_label' => 'Menú',
    'pages' => [
        'edit' => [
            'title' => 'Editar elemento de menú',
        ],
    ],
    'sections' => [
        'main' => [
            'label' => 'Información básica',
        ],
        'link' => [
            'label' => 'Configuración del enlace',
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
            'label' => 'Etiqueta del menú',
        ],
        'parent_id' => [
            'label' => 'Elemento madre',
            'placeholder' => 'Nivel superior',
        ],
        'type' => [
            'label' => 'Tipo de enlace',
            'options' => [
                'manual' => 'URL libre',
                'route' => 'Página estática',
                'model' => 'Página de contenido',
            ],
        ],
        'url' => [
            'label' => 'URL',
        ],
        'route' => [
            'label' => 'Página estática',
            'options' => [
                'testa_storefront_homepage' => 'Portada',
                'testa_storefront_bookshop_homepage' => 'Librería / Portada',
                'testa_storefront_bookshop_itineraries_index' => 'Librería / Lista de itinerarios',
                'testa_storefront_bookshop_search' => 'Librería / Buscador',
                'testa_storefront_editorial_homepage' => 'Editorial / Portada',
                'testa_storefront_editorial_authors_index' => 'Editorial / Lista de autoras',
                'testa_storefront_education_homepage' => 'Formación / Portada',
                'testa_storefront_education_topics_index' => 'Formación / Lista de temas',
                'testa_storefront_education_courses_index' => 'Formación / Lista de cursos',
                'testa_storefront_media_homepage' => 'Mediateca / Portada',
                'testa_storefront_media_search' => 'Mediateca / Buscador',
                'testa_storefront_media_documents_index' => 'Mediateca / Lista de documentos',
                'testa_storefront_news_homepage' => 'Actualidad / Portada',
                'testa_storefront_activities_index' => 'Actualidad / Lista de actividades',
                'testa_storefront_articles_index' => 'Actualidad / Lista de noticias',
                'testa_storefront_checkout_summary' => 'Carrito / Resumen',
                'testa_storefront_checkout_shipping-and-payment' => 'Carrito / Envío y pago',
                'testa_storefront_membership_signup' => 'Apoya el proyecto / Hazte socix',
                'testa_storefront_membership_donate' => 'Apoya el proyecto / Dona',
            ],
        ],
        'linkable_type' => [
            'label' => 'Tipo de contenido',
            'options' => [
                'page' => 'Página',
                'collection' => 'Grupos de categorías',
            ],
        ],
        'linkable_id' => [
            'label' => 'Contenido',
        ],
        'is_published' => [
            'label' => 'Público',
        ],
    ],
];
