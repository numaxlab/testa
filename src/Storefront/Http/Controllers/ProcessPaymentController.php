<?php

namespace Trafikrak\Storefront\Http\Controllers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Lunar\Base\PaymentTypeInterface;
use Lunar\Exceptions\FingerprintMismatchException;
use Lunar\Facades\Payments;
use Lunar\Models\Cart;
use Lunar\Models\Order;
use NumaxLab\Lunar\Redsys\RedsysPayment;
use NumaxLab\Lunar\Redsys\Responses\RedirectToPaymentGateway;
use Trafikrak\Models\Membership\MembershipPlan;
use Trafikrak\Storefront\Livewire\Membership\DonatePage;

class ProcessPaymentController
{
    public function __invoke(Request $request, $id)
    {
        $cart = Cart::findOrFail($id);
        $cart->calculate();

        try {
            $cart->checkFingerprint($request->input('fingerprint'));
        } catch (FingerprintMismatchException $e) {
            $this->clearCart($cart);

            return abort(403);
        }

        if (Auth::user()->id != $cart->user_id) {
            $this->clearCart($cart);

            return abort(403);
        }

        $order = $cart->draftOrder()->first();

        if (! $order) {
            $order = $cart->createOrder();
        }

        $paymentType = $request->input('payment');

        try {
            $data = match (config("lunar.payments.types.{$paymentType}.driver")) {
                RedsysPayment::DRIVER_NAME => $this->preparedRedsysData($paymentType, $order),
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
            ->route($this->guessPaymentSuccessRouteNameFromOrder($order), $order->fingerprint);
    }

    private function clearCart(Cart $cart): void
    {
        $cart->clear();
        $cart->delete();
    }

    private function preparedRedsysData(string $paymentType, Order $order): array
    {
        return [
            'config_key' => 'default',
            'url_ok' => route($this->guessPaymentSuccessRouteNameFromOrder($order), $order->fingerprint),
            'url_ko' => url()->previous(),
            'method' => $paymentType === 'bizum' ? 'z' : 'C',
            'product_description' => 'Compra online en '.config('app.name'),
        ];
    }

    private function guessPaymentSuccessRouteNameFromOrder(Order $order): string
    {
        foreach ($order->lines as $line) {
            if ($line->purchasable_type === 'product_variant') {
                if (Str::contains($line->purchasable->sku, DonatePage::DONATION_PRODUCT_SKU)) {
                    return 'trafikrak.storefront.membership.donate.success';
                }

                return 'trafikrak.storefront.checkout.success';
            }

            if ($line->purchasable_type === Relation::getMorphAlias(MembershipPlan::class)) {
                return 'trafikrak.storefront.membership.signup.success';
            }
        }

        return 'trafikrak.storefront.checkout.success';
    }

    private function prepareOfflineData(mixed $paymentType, Order $order): array
    {
        return [];
    }
}
