<?php

namespace Testa\Storefront\UseCases\Membership;

use Illuminate\Contracts\Auth\Authenticatable;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Cart;
use Lunar\Models\CartAddress;
use Testa\Models\Membership\MembershipPlan;
use Testa\Storefront\Data\MembershipSignupData;

final class SignupMembership
{
    public function execute(Authenticatable $user, MembershipSignupData $data): Cart
    {
        $meta = [
            'Tipo de pedido' => 'Subscripción socias',
            'Método de pago' => __("testa::global.payment_types.{$data->paymentType}.title"),
            'DNI/NIF' => $data->idNumber,
        ];

        if ($data->paymentType === 'direct-debit') {
            $meta['Titular de la cuenta'] = $data->directDebitOwnerName;
            $meta['Banco'] = $data->directDebitBankName;
            $meta['IBAN'] = $data->directDebitIban;
        }

        $cart = Cart::create([
            'user_id' => $user->id,
            'customer_id' => $user->latestCustomer()?->id,
            'currency_id' => StorefrontSession::getCurrency()->id,
            'channel_id' => StorefrontSession::getChannel()->id,
            'meta' => $meta,
        ]);

        $membershipPlan = MembershipPlan::find($data->membershipPlanId);
        $cart->add($membershipPlan->variant);

        $billing = new CartAddress();
        $billing->fill($data->billingAddress->toArray());
        $cart->setBillingAddress($billing);

        $cart->calculate();

        return $cart;
    }
}
