<?php

namespace Testa\Contracts\Payment;

use Lunar\Models\Cart;
use Lunar\Models\Order;

final readonly class PaymentContext
{
    public function __construct(
        public string $paymentType,
        public Order $order,
        public Cart $cart,
        public string $successRoute,
        public string $failureRoute,
        public ?string $orderType = null,
    ) {}

    public static function fromOrderAndCart(
        string $paymentType,
        Order $order,
        Cart $cart,
        array $successRouteMapping,
        array $failureRouteMapping,
    ): self {
        $orderType = $order->meta['Tipo de pedido'] ?? null;

        $successRoute = $successRouteMapping[$orderType] ?? $successRouteMapping['default'];
        $failureRoute = $failureRouteMapping[$orderType] ?? $failureRouteMapping['default'];

        return new self(
            paymentType: $paymentType,
            order: $order,
            cart: $cart,
            successRoute: route($successRoute, $order->fingerprint),
            failureRoute: route($failureRoute),
            orderType: $orderType,
        );
    }
}
