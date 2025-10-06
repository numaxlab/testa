<?php

namespace Trafikrak\Storefront\Livewire\Checkout;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Lunar\Base\PaymentTypeInterface;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\CartSession;
use Lunar\Facades\Payments;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\CartAddress;
use Lunar\Models\Contracts\Cart;
use Lunar\Models\Country;
use Lunar\Models\Order;
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

    public string $paymentType = 'cash-in-hand';
    public $payment_intent = null;
    public $payment_intent_client_secret = null;
    protected $listeners = [
        'cartUpdated' => 'refreshCart',
        'selectedShippingOption' => 'refreshCart',
    ];
    protected $queryString = [
        'payment_intent',
        'payment_intent_client_secret',
    ];

    public function getCountriesProperty(): Collection
    {
        return Country::orderBy('native')->get();
    }

    public function mount(): void
    {
        $this->cart = CartSession::current();

        if (!$this->cart) {
            $this->redirect('/');

            return;
        }

        if ($this->payment_intent) {
            $payment = Payments::driver($this->paymentType)->cart($this->cart)->withData([
                'payment_intent_client_secret' => $this->payment_intent_client_secret,
                'payment_intent' => $this->payment_intent,
            ])->authorize();

            if ($payment->success) {
                redirect()->route('checkout-success.view');

                return;
            }
        }

        $this->shipping->init();
        $this->billing->init();

        if ($this->cart->shippingAddress) {
            $this->shipping->fill($this->cart->shippingAddress->toArray());
        }
        if ($this->cart->billingAddress) {
            $this->billing->fill($this->cart->billingAddress->toArray());
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
            ->mapWithKeys(fn($value, $key) => ["$type.$key" => $value])
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

        $this->determineCheckoutStep();
    }

    public function getShippingOptionsProperty(): Collection
    {
        return ShippingManifest::getOptions($this->cart);
    }

    public function getShippingOptionProperty(): ?ShippingOption
    {
        $shippingAddress = $this->cart->shippingAddress;

        if (!$shippingAddress) {
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

    public function checkout()
    {
        /** @var PaymentTypeInterface $paymentDriver */
        $paymentDriver = Payments::driver($this->paymentType)
            ->cart($this->cart)
            ->withData([
                'payment_intent_client_secret' => $this->payment_intent_client_secret,
                'payment_intent' => $this->payment_intent,
            ]);

        $payment = $paymentDriver->authorize();

        if ($payment->success) {
            $order = Order::findOrFail($payment->orderId);

            return redirect()->route('trafikrak.storefront.checkout.success', $order->fingerprint);
        }

        return redirect()->route('trafikrak.storefront.checkout.shipping-and-payment');
    }
}
