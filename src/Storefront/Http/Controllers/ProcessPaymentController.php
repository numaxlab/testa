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
use Testa\Contracts\Payment\PaymentContext;
use Testa\Contracts\Payment\PaymentResultType;
use Testa\Payment\PaymentGatewayRegistry;

class ProcessPaymentController
{
    private const array SUCCESS_ROUTES = [
        'Pedido librería' => 'testa.storefront.checkout.success',
        'Curso' => 'testa.storefront.education.courses.register.success',
        'Subscripción socias' => 'testa.storefront.membership.signup.success',
        'Donación' => 'testa.storefront.membership.donate.success',
        'default' => 'testa.storefront.checkout.success',
    ];

    private const array CHECKOUT_ROUTES = [
        'Pedido librería' => 'testa.storefront.checkout.shipping-and-payment',
        'Curso' => 'testa.storefront.education.courses.register',
        'Subscripción socias' => 'testa.storefront.membership.signup',
        'Donación' => 'testa.storefront.membership.donate',
        'default' => 'testa.storefront.checkout.shipping-and-payment',
    ];

    public function __construct(
        private readonly PaymentGatewayRegistry $gatewayRegistry,
    ) {}

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
                $adapter = $this->gatewayRegistry->getAdapterForPaymentType($paymentType);
            } catch (InvalidArgumentException $e) {
                Log::error($e->getMessage());

                return redirect()->back()->withErrors(['payment' => __('Error al procesar el pago')]);
            }

            $context = PaymentContext::fromOrderAndCart(
                paymentType: $paymentType,
                order: $order,
                cart: $cart,
                successRouteMapping: self::SUCCESS_ROUTES,
                failureRouteMapping: self::CHECKOUT_ROUTES,
            );

            $data = $adapter->prepareAuthorizationData($context);

            /** @var PaymentTypeInterface $paymentDriver */
            $paymentDriver = Payments::driver($paymentType)
                ->cart($cart)
                ->order($order)
                ->withData($data);

            $response = $paymentDriver->authorize();

            $result = $adapter->handleAuthorizationResponse($response, $paymentDriver, $context);

            return match ($result->type) {
                PaymentResultType::Redirect => $result->paymentDriver->redirect(),
                PaymentResultType::Success => $this->handleSuccess($cart, $result->orderId),
                PaymentResultType::Pending => $this->handleSuccess($cart, $result->orderId),
                PaymentResultType::Failure => abort(401),
            };
        });
    }

    private function guessCheckoutRouteNameFromCart(Cart $cart): string
    {
        $orderType = $cart->meta['Tipo de pedido'] ?? null;

        return self::CHECKOUT_ROUTES[$orderType] ?? self::CHECKOUT_ROUTES['default'];
    }

    private function handleSuccess(Cart $cart, int $orderId): \Illuminate\Http\RedirectResponse
    {
        $order = Order::findOrFail($orderId);

        $this->clearCart($cart);

        return redirect()->route($this->getSuccessRouteName($order), $order->fingerprint);
    }

    private function clearCart(Cart $cart): void
    {
        $cart->clear();
        $cart->delete();
    }

    private function getSuccessRouteName(Order $order): string
    {
        $orderType = $order->meta['Tipo de pedido'] ?? null;

        return self::SUCCESS_ROUTES[$orderType] ?? self::SUCCESS_ROUTES['default'];
    }
}
