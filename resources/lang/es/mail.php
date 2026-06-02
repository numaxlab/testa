<?php

return [
    'order_confirmation' => [
        'subject' => 'Confirmación de pedido #:reference',
        'greeting' => '¡Gracias por tu pedido!',
        'intro' => 'Hemos recibido tu pedido #:reference y está siendo procesado.',
        'shipping_address' => 'Dirección de envío',
        'billing_address' => 'Datos de facturación',
        'footer' => 'Gracias por confiar en nosotros.',
    ],
    'admin_order_notification' => [
        'subject' => 'Nuevo pedido #:reference',
        'heading' => 'Nuevo pedido #:reference',
        'date' => 'Fecha',
        'customer' => 'Cliente',
        'payment_method' => 'Forma de pago',
        'view_order' => 'Ver pedido en el panel',
    ],
    'order_pending_payment' => [
        'subject' => 'Pedido recibido #:reference — pago pendiente',
        'greeting' => 'Hemos recibido tu pedido',
        'intro' => 'Tu pedido #:reference ha sido registrado y está pendiente de confirmación de pago. En cuanto verifiquemos el ingreso, procederemos con su tramitación.',
        'footer' => 'Si tienes alguna duda, no dudes en ponerte en contacto con nosotros.',
    ],
    'admin_pending_order_notification' => [
        'subject' => 'Pedido pendiente #:reference (pago offline)',
        'heading' => 'Pedido pendiente de pago #:reference',
    ],
    'order_lines' => [
        'product' => 'Producto',
        'qty' => 'Cant.',
        'price' => 'Precio',
        'total' => 'Total',
    ],
];
