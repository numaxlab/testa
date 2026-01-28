<?php

namespace Testa\Storefront\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Lunar\Base\PaymentTypeInterface;
use Lunar\Exceptions\FingerprintMismatchException;
use Lunar\Facades\Payments;
use Lunar\Models\Cart;
use Lunar\Models\Order;
use NumaxLab\Lunar\Redsys\RedsysPayment;
use NumaxLab\Lunar\Redsys\Responses\RedirectToPaymentGateway;

class ProcessPaymentController
{
    private const ORDER_TYPE_ROUTES = [
        'Pedido librería' => 'testa.storefront.checkout.success',
        'Curso' => 'testa.storefront.education.courses.register.success',
        'Subscripción socias' => 'testa.storefront.membership.signup.success',
        'Donación' => 'testa.storefront.membership.donate.success',
    ];

    private const CHECKOUT_ROUTES = [
        'Pedido librería' => 'testa.storefront.checkout.shipping-and-payment',
        'Curso' => 'testa.storefront.education.courses.register',
        'Subscripción socias' => 'testa.storefront.membership.signup',
        'Donación' => 'testa.storefront.membership.donate',
    ];

    public function __invoke(Request $request, $id)
    {
        $cart = Cart::findOrFail($id);
        $cart->calculate();

        try {
            $cart->checkFingerprint($request->input('fingerprint'));
        } catch (FingerprintMismatchException $e) {
            return redirect()
                ->route($this->guessCheckoutRouteNameFromCart($cart))
                ->withErrors(['fingerprint' => __('El carrito ha sido modificado. Por favor, revisa tu pedido.')]);
        }

        if (Auth::user()->id != $cart->user_id) {
            return abort(403);
        }

        return DB::transaction(function () use ($cart, $request) {
            $order = $cart->draftOrder()->first();

            if (! $order) {
                $order = $cart->createOrder();
            }

            $paymentType = $request->input('payment');

            try {
                $data = match (config("lunar.payments.types.{$paymentType}.driver")) {
                    RedsysPayment::DRIVER_NAME => $this->preparedRedsysData($paymentType, $order, $cart),
                    'offline' => $this->prepareOfflineData($paymentType, $order),
                    default => throw new InvalidArgumentException("Driver de pago no válido: $paymentType"),
                };
            } catch (InvalidArgumentException $e) {
                Log::error($e->getMessage());
                return redirect()->back()->withErrors(['payment' => __('Error al procesar el pago')]);
            }

            /** @var PaymentTypeInterface $paymentDriver */
            $paymentDriver = Payments::driver($paymentType)
                ->cart($cart)
                ->order($order)
                ->withData($data);

            $response = $paymentDriver->authorize();

            if (! $response->success) {
                return abort(401);
            }

            if (is_a($response, RedirectToPaymentGateway::class)) {
                return $paymentDriver->redirect();
            }

            $order = Order::findOrFail($response->orderId);

            $this->clearCart($cart);

            return redirect()
                ->route($this->getSuccessRouteName($order), $order->fingerprint);
        });
    }

    private function guessCheckoutRouteNameFromCart(Cart $cart): string
    {
        $orderType = $cart->meta['Tipo de pedido'] ?? null;

        return self::CHECKOUT_ROUTES[$orderType] ?? 'testa.storefront.checkout.shipping-and-payment';
    }

    private function preparedRedsysData(string $paymentType, Order $order, Cart $cart): array
    {
        return [
            'config_key' => 'default',
            'url_ok' => route($this->getSuccessRouteName($order), $order->fingerprint),
            'url_ko' => route($this->guessCheckoutRouteNameFromCart($cart)),
            'method' => $paymentType === 'bizum' ? 'z' : 'C',
            'product_description' => 'Compra online en '.config('app.name'),
        ];
    }

    private function getSuccessRouteName(Order $order): string
    {
        $orderType = $order->meta['Tipo de pedido'] ?? null;

        return self::ORDER_TYPE_ROUTES[$orderType] ?? 'testa.storefront.checkout.success';
    }

    private function prepareOfflineData(mixed $paymentType, Order $order): array
    {
        return [];
    }

    private function clearCart(Cart $cart): void
    {
        $cart->clear();
        $cart->delete();
    }
}
