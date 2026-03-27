<?php

namespace Testa\Storefront\UseCases\Education;

use Illuminate\Contracts\Auth\Authenticatable;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Cart;
use Lunar\Models\CartAddress;
use Lunar\Models\Country;
use Testa\Models\Education\Course;
use Testa\Settings\ContactSettings;
use Testa\Storefront\Data\CourseRegistrationData;

final class RegisterForCourse
{
    public function execute(Authenticatable $user, Course $course, CourseRegistrationData $data): Cart
    {
        $cart = Cart::create([
            'user_id' => $user->id,
            'customer_id' => $user->latestCustomer()?->id,
            'currency_id' => StorefrontSession::getCurrency()->id,
            'channel_id' => StorefrontSession::getChannel()->id,
            'meta' => [
                'Factura' => $data->invoice ? 'Si' : 'No',
                'Tipo de pedido' => 'Curso',
                'Método de pago' => __("testa::global.payment_types.{$data->paymentType}.title"),
            ],
        ]);

        foreach ($course->purchasable->variants as $variant) {
            if ($variant->id == $data->selectedVariantId) {
                $cart->add($variant);
                break;
            }
        }

        $billing = new CartAddress();

        if ($data->invoice && $data->billingAddress !== null) {
            $billing->fill($data->billingAddress->toArray());
        } else {
            $contactSettings = app(ContactSettings::class);
            $primaryAddress = $contactSettings->getPrimaryAddress();
            $billing->first_name = $user->latestCustomer()->first_name;
            $billing->country_id = Country::where('iso2', $primaryAddress['country_iso2'])->firstOrFail()->id;
            $billing->city = $primaryAddress['city'];
            $billing->postcode = $primaryAddress['postcode'];
            $billing->line_one = $primaryAddress['line_one'];
        }

        $cart->setBillingAddress($billing);

        $cart->calculate();

        return $cart;
    }
}
