<?php

namespace Testa\Storefront\UseCases\Membership;

use Illuminate\Contracts\Auth\Authenticatable;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Cart;
use Lunar\Models\CartAddress;
use Lunar\Models\Country;
use Lunar\Models\Product;
use Testa\Settings\ContactSettings;
use Testa\Storefront\Data\DonationData;
use Testa\Storefront\Queries\Membership\GetDonationProduct;

final class PlaceDonation
{
    public function execute(Authenticatable $user, Product $product, DonationData $data): Cart
    {
        $cart = Cart::create([
            'user_id' => $user->id,
            'customer_id' => $user->latestCustomer()?->id,
            'currency_id' => StorefrontSession::getCurrency()->id,
            'channel_id' => StorefrontSession::getChannel()->id,
            'meta' => [
                'Tipo de pedido' => 'Donación',
                'Método de pago' => __("testa::global.payment_types.{$data->paymentType}.title"),
                'DNI/NIF' => $data->idNumber,
                'Comentarios' => $data->comments,
            ],
        ]);

        if ($data->selectedQuantity === 'free') {
            $variant = $product->variants->firstWhere('sku', GetDonationProduct::DONATION_SKU);
            $unitPriceInCents = (int) ($data->freeQuantityValue * 100);
            $cart->add($variant, 1, ['unit_price' => $unitPriceInCents]);
        } else {
            $variant = $product->variants->find($data->selectedQuantity);
            $cart->add($variant);
        }

        $contactSettings = app(ContactSettings::class);
        $primaryAddress = $contactSettings->getPrimaryAddress();

        $billing = new CartAddress();
        $billing->first_name = $user->latestCustomer()->first_name;
        $billing->country_id = Country::where('iso2', $primaryAddress['country_iso2'])->firstOrFail()->id;
        $billing->city = $primaryAddress['city'];
        $billing->postcode = $primaryAddress['postcode'];
        $billing->line_one = $primaryAddress['line_one'];
        $cart->setBillingAddress($billing);

        $cart->calculate();

        return $cart;
    }
}
