<?php

namespace Trafikrak\Storefront\Livewire\Checkout;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\CartSession;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\CartAddress;
use Lunar\Models\Contracts\Cart;
use Lunar\Models\Country;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Storefront\Livewire\Checkout\Forms\AddressForm;

class ShippingAndPaymentPage extends Page
{
    public ?Cart $cart;

    public AddressForm $shipping;

    public AddressForm $billing;

    public int $currentStep = 1;

    public bool $shippingIsBilling = true;

    public $chosenShipping = null;

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

        $this->paymentTypes = config('trafikrak.payment_types.store');

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

    public function determineCheckoutStep(): void
    {
        $shippingAddress = $this->cart->shippingAddress;
        $billingAddress = $this->cart->billingAddress;

        if ($shippingAddress) {
            if ($shippingAddress->id) {
                $this->currentStep = $this->steps['shipping_address'] + 1;
            }

            if ($this->shippingOption) {
                $this->chosenShipping = $this->shippingOption->getIdentifier();
                $this->currentStep = $this->steps['shipping_option'] + 1;
            } else {
                $this->currentStep = $this->steps['shipping_option'];
                $this->chosenShipping = $this->shippingOptions->first()?->getIdentifier();

                return;
            }
        }

        if ($billingAddress) {
            $this->currentStep = $this->steps['billing_address'] + 1;
        }
    }

    public function updated($field, $value): void
    {
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
        return view('trafikrak::storefront.livewire.checkout.shipping-and-payment');
    }

    public function saveAddress(string $type): void
    {
        $rules = collect($this->shipping->getRules())
            ->mapWithKeys(fn ($value, $key) => ["$type.$key" => $value])
            ->toArray();

        $this->validate($rules);

        if ($type == 'billing') {
            $billing = new CartAddress();
            $billing->fill($this->shipping->all());
            $this->cart->setBillingAddress($billing);

            $this->billing->fill($this->cart->billingAddress->toArray());
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
        $option = $this->shippingOptions->first(fn ($option) => $option->getIdentifier() == $this->chosenShipping);

        CartSession::setShippingOption($option);

        $this->refreshCart();

        $this->determineCheckoutStep();
    }

    public function refreshCart(): void
    {
        $this->cart = CartSession::current();
    }
}
