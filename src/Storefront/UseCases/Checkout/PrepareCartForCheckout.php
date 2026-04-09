<?php

namespace Testa\Storefront\UseCases\Checkout;

use Lunar\Models\Contracts\Cart;
use Lunar\Shipping\Models\ShippingMethod;
use RuntimeException;
use Testa\Storefront\Data\CartCheckoutData;

final class PrepareCartForCheckout
{
    public function execute(Cart $cart, CartCheckoutData $data): void
    {
        if ($data->shippingMethod !== 'send') {
            $cart->setShippingAddress($cart->billingAddress);

            $shippingMethod = ShippingMethod::find($data->shippingMethod);

            if (!$shippingMethod) {
                throw new RuntimeException('Shipping method not found.');
            }

            $shippingRate = $shippingMethod->shippingRates->first();

            if (!$shippingRate) {
                throw new RuntimeException('Shipping rate not found.');
            }

            $cart->setShippingOption($shippingRate->getShippingOption($cart));
        }

        $cart->meta = [
            'Tipo de pedido' => 'Pedido librería',
            'Método de pago' => __("testa::global.payment_types.{$data->paymentType}.title"),
            'Solicita factura' => $data->wantsInvoice ? __('Sí') : __('No'),
            'Es un regalo' => $data->isGift ? __('Sí') : __('No'),
        ];

        $cart->save();
        $cart->calculate();
    }
}
