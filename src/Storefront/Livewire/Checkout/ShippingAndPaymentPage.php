<?php

namespace Testa\Storefront\Livewire\Checkout;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\CartSession;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\CartAddress;
use Lunar\Models\Contracts\Cart;
use Lunar\Models\Country;
use Lunar\Shipping\Models\ShippingMethod;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Settings\PaymentSettings;
use Testa\Storefront\Livewire\Checkout\Forms\AddressForm;

class ShippingAndPaymentPage extends Page
{
    private const int GESLIB_PRODUCT_TYPE_ID = 1;

    public ?Cart $cart;

    public AddressForm $shipping;

    public AddressForm $billing;

    public int $currentStep = 1;

    public string $shippingMethod = 'send';

    public bool $shippingIsBilling = true;

    public ?string $chosenShipping = null;

    public ?string $couponCode = null;

    public ?string $paymentType = null;

    public array $steps = [
        'shipping_address' => 1,
        'shipping_option' => 2,
        'billing_address' => 3,
        'payment' => 4,
    ];

    public array $paymentTypes = [];

    protected $listeners = [
        'cartUpdated' => 'refreshCart',
        'selectedShippingOption' => 'refreshCart',
    ];

    public function getCountriesProperty(): Collection
    {
        return Country::orderBy('native')->get();
    }

    public function mount(): void
    {
        $this->cart = CartSession::current();

        if (! $this->cart || $this->cart->lines->isEmpty()) {
            $this->redirect('/');

            return;
        }

        $this->removeNonGeslibItems();

        if ($this->cart->lines->isEmpty()) {
            $this->redirect('/');

            return;
        }

        $this->paymentTypes = app(PaymentSettings::class)->store;

        if (! Auth::user()?->latestCustomer()?->canBuyOnCredit()) {
            $this->paymentTypes = array_values(array_filter(
                $this->paymentTypes,
                fn($type) => $type !== 'credit',
            ));
        }

        $this->shipping->init();
        $this->billing->init();

        if ($this->cart->shippingAddress) {
            $this->shipping->fill($this->cart->shippingAddress->toArray());
        }
        if ($this->cart->billingAddress) {
            $this->billing->fill($this->cart->billingAddress->toArray());
        }

        if (! $this->shipping->contact_email) {
            $this->shipping->contact_email = $this->cart->user->email;
        }
        if (! $this->billing->contact_email) {
            $this->billing->contact_email = $this->cart->user->email;
        }

        $this->determineCheckoutStep();
    }

    private function removeNonGeslibItems(): void
    {
        $cartLines = $this->cart->lines()->with('purchasable.product')->get();

        $invalidLineIds = $cartLines->filter(function ($line) {
            if ($line->purchasable_type !== 'product_variant') {
                return true;
            }

            return ! $line->purchasable || $line->purchasable->product?->product_type_id !== self::GESLIB_PRODUCT_TYPE_ID;
        })->pluck('id');

        foreach ($invalidLineIds as $lineId) {
            CartSession::remove($lineId);
        }

        if ($invalidLineIds->isNotEmpty()) {
            $this->cart = CartSession::current();
        }
    }

    public function determineCheckoutStep(): void
    {
        $shippingAddress = $this->cart->shippingAddress;
        $billingAddress = $this->cart->billingAddress;

        if ($this->shippingMethod !== 'send') {
            $this->currentStep = $this->steps['billing_address'];

            if ($billingAddress) {
                $this->currentStep = $this->steps['billing_address'] + 1;
            }

            return;
        }

        $this->currentStep = $this->steps['shipping_address'];

        if (! $shippingAddress) {
            return;
        }

        $this->currentStep = $this->steps['shipping_address'] + 1;

        if (! $this->shippingOption) {
            $this->currentStep = $this->steps['shipping_option'];
            return;
        }

        $this->chosenShipping = $this->shippingOption->getIdentifier();
        $this->currentStep = $this->steps['shipping_option'] + 1;

        if ($billingAddress) {
            $this->currentStep = $this->steps['billing_address'] + 1;
        }
    }

    public function updated($field, $value): void
    {
        if ($field === 'shippingMethod') {
            $this->determineCheckoutStep();
        }
        if ($field === 'shipping.customer_address_id') {
            $this->shipping->loadAddress($value);
        }
        if ($field === 'billing.customer_address_id') {
            $this->billing->loadAddress($value);
        }
        if ($field === 'shipping.country_id') {
            $this->shipping->loadStates($value);
        }
        if ($field === 'billing.country_id') {
            $this->billing->loadStates($value);
        }
        if ($field === 'couponCode') {
            $this->cart->coupon_code = $value;
            $this->cart->save();
            $this->cart->calculate();
        }
    }

    public function hydrate(): void
    {
        $this->cart = CartSession::current();
    }

    public function triggerAddressRefresh(): void
    {
        $this->dispatch('refreshAddress');
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.checkout.shipping-and-payment');
    }

    public function saveAddress(string $type): void
    {
        $rules = collect($this->shipping->getRules())
            ->mapWithKeys(fn($value, $key) => ["$type.$key" => $value])
            ->toArray();

        $this->validate($rules);

        if ($type == 'billing') {
            $this->shippingIsBilling = false;

            $billing = new CartAddress();
            $billing->fill($this->billing->all());

            $this->cart->setBillingAddress($billing);
        }

        if ($type == 'shipping') {
            $shipping = new CartAddress();
            $shipping->fill($this->shipping->all());
            $this->cart->setShippingAddress($shipping);

            $this->shipping->fill($this->cart->shippingAddress->toArray());

            if ($this->shippingIsBilling) {
                $billing = $this->cart->billingAddress;

                if ($billing) {
                    $billing->fill($this->shipping->all());
                } else {
                    $billing = clone $shipping;
                }

                $this->cart->setBillingAddress($billing);
                $this->billing->fill($this->cart->billingAddress->toArray());
            }
        }

        if ($type == 'shipping' && $this->shipping->saveToUser) {
            $this->shipping->store();
        }
        if ($type == 'billing' && $this->billing->saveToUser) {
            $this->billing->store();
        }

        $this->determineCheckoutStep();
    }

    public function getPickupOptionsProperty(): Collection
    {
        return ShippingMethod::where('driver', 'collection')->get();
    }

    public function getShippingOptionsProperty(): Collection
    {
        return ShippingManifest::getOptions($this->cart);
    }

    public function getShippingOptionProperty(): ?ShippingOption
    {
        $shippingAddress = $this->cart->shippingAddress;

        if (! $shippingAddress) {
            return null;
        }

        $option = $shippingAddress->shipping_option;

        if ($option) {
            return ShippingManifest::getOptions($this->cart)->first(function ($opt) use ($option) {
                return $opt->getIdentifier() == $option;
            });
        }

        return null;
    }

    public function saveShippingOption(): void
    {
        $option = $this->shippingOptions->first(fn($option) => $option->getIdentifier() == $this->chosenShipping);

        CartSession::setShippingOption($option);

        $this->refreshCart();

        $this->determineCheckoutStep();
    }

    public function refreshCart(): void
    {
        $this->cart = CartSession::current();
    }

    public function finish(): null|RedirectResponse|Redirector
    {
        if ($this->currentStep < $this->steps['payment']) {
            $this->dispatch('uncompleted-steps');
            return null;
        }

        if (! $this->paymentType) {
            $this->dispatch('uncompleted-steps');
            return null;
        }

        if ($this->shippingMethod !== 'send') {
            $this->cart->setShippingAddress($this->cart->billingAddress);

            $shippingMethod = ShippingMethod::where('id', $this->shippingMethod)->firstOrFail();
            $shippingOption = $shippingMethod->shippingRates->first()->getShippingOption($this->cart);

            $this->cart->setShippingOption($shippingOption);
        }

        $this->cart->meta = [
            'Tipo de pedido' => 'Pedido librería',
            'Método de pago' => __("testa::global.payment_types.{$this->paymentType}.title"),
        ];

        $this->cart->save();

        $this->cart->calculate();

        $fingerprint = $this->cart->fingerprint();

        return redirect()
            ->route(
                'testa.storefront.checkout.process-payment',
                ['id' => $this->cart->id, 'fingerprint' => $fingerprint, 'payment' => $this->paymentType],
            );
    }
}
