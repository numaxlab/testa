<?php

namespace Testa\Storefront\Livewire\Checkout;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\CartSession;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\CartAddress;
use Lunar\Models\Contracts\Cart;
use Lunar\Shipping\Models\ShippingMethod;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Settings\PaymentSettings;
use Testa\Storefront\Livewire\Checkout\Forms\AddressForm;
use Testa\Storefront\Livewire\Concerns\FiltersGeslibProducts;

class ShippingAndPaymentPage extends Page
{
    use FiltersGeslibProducts;

    public ?Cart $cart;

    public AddressForm $shipping;

    public AddressForm $billing;

    public int $currentStep = 1;

    public string $shippingMethod = 'send';

    public bool $shippingIsBilling = true;

    public ?string $chosenShipping = null;

    public ?string $couponCode = null;

    public ?string $paymentType = null;

    public bool $wantsInvoice = false;

    public bool $isGift = false;

    public array $steps = [
        'shipping_address' => 1,
        'shipping_option' => 2,
        'billing_address' => 3,
        'payment' => 4,
    ];

    public array $paymentTypes = [];

    public function mount(): void
    {
        $this->cart = CartSession::current();

        if (! $this->cart || $this->cart->lines->isEmpty()) {
            $this->redirect('/');

            return;
        }

        $this->removeNonGeslibItems();

        $this->cart = CartSession::current();

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
            if ($this->shipping->country_id) {
                $savedState = $this->shipping->state;
                $this->shipping->loadStates($this->shipping->country_id);
                $this->shipping->state = $savedState;
            }
        }
        if ($this->cart->billingAddress) {
            $this->billing->fill($this->cart->billingAddress->toArray());
            if ($this->billing->country_id) {
                $savedState = $this->billing->state;
                $this->billing->loadStates($this->billing->country_id);
                $this->billing->state = $savedState;
            }
        }

        if (! $this->shipping->contact_email) {
            $this->shipping->contact_email = $this->cart->user->email;
        }
        if (! $this->billing->contact_email) {
            $this->billing->contact_email = $this->cart->user->email;
        }

        $customer = $this->cart->user?->latestCustomer();

        if ($customer) {
            if (! $this->shipping->first_name) {
                $this->shipping->first_name = $customer->first_name ?? '';
            }
            if (! $this->shipping->last_name) {
                $this->shipping->last_name = $customer->last_name ?? '';
            }
            if (! $this->shipping->company_name) {
                $this->shipping->company_name = $customer->company_name;
            }
            if (! $this->shipping->tax_identifier) {
                $this->shipping->tax_identifier = $customer->tax_identifier;
            }

            if (! $this->billing->first_name) {
                $this->billing->first_name = $customer->first_name ?? '';
            }
            if (! $this->billing->last_name) {
                $this->billing->last_name = $customer->last_name ?? '';
            }
            if (! $this->billing->company_name) {
                $this->billing->company_name = $customer->company_name;
            }
            if (! $this->billing->tax_identifier) {
                $this->billing->tax_identifier = $customer->tax_identifier;
            }
        }

        $this->determineCheckoutStep();
    }

    public function determineCheckoutStep(): void
    {
        $shippingAddress = $this->cart->shippingAddress;
        $billingAddress = $this->cart->billingAddress;

        if ($this->shippingMethod !== 'send') {
            $this->currentStep = $this->steps['billing_address'];

            if ($billingAddress) {
                $this->currentStep = $this->steps['payment'];
            }

            return;
        }

        $this->currentStep = $this->steps['shipping_address'];

        if (! $shippingAddress) {
            return;
        }

        $this->currentStep = $this->steps['shipping_option'];

        if (! $this->shippingOption) {
            return;
        }

        $this->chosenShipping = $this->shippingOption->getIdentifier();
        $this->currentStep = $this->steps['billing_address'];

        if ($billingAddress) {
            $this->currentStep = $this->steps['payment'];
        }
    }

    public function updated($field, $value): void
    {
        match ($field) {
            'shippingMethod' => $this->onShippingMethodChanged($value),
            'shipping.customer_address_id' => $this->shipping->loadAddress($value),
            'billing.customer_address_id' => $this->billing->loadAddress($value),
            'shipping.country_id' => $this->shipping->loadStates($value),
            'billing.country_id' => $this->billing->loadStates($value),
            'couponCode' => $this->saveCouponCode($value),
            default => null,
        };
    }

    private function onShippingMethodChanged(string $value): void
    {
        $this->shippingMethod = $value;
        $this->chosenShipping = null;

        if ($this->cart->shippingAddress && $this->cart->shippingAddress->shipping_option) {
            $this->cart->shippingAddress->shipping_option = null;
            $this->cart->shippingAddress->save();
        }

        $this->cart->shippingBreakdown = null;
        $this->cart->refresh()->recalculate();
        $this->determineCheckoutStep();
    }

    private function saveCouponCode(?string $value): void
    {
        $this->cart->coupon_code = $value;
        $this->cart->save();
        $this->cart->calculate();
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
        $rules = collect($this->{$type}->getRules())
            ->mapWithKeys(fn($value, $key) => ["$type.$key" => $value])
            ->toArray();

        $this->validate($rules);

        if ($type === 'billing') {
            $this->shippingIsBilling = false;

            $billing = new CartAddress();
            $billing->fill($this->billing->all());

            $this->cart->setBillingAddress($billing);
        }

        if ($type === 'shipping') {
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

        if ($type === 'shipping' && $this->shipping->saveToUser) {
            $this->shipping->store();
        }
        if ($type === 'billing' && $this->billing->saveToUser) {
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
                return $opt->getIdentifier() === $option;
            });
        }

        return null;
    }

    public function saveShippingOption(): void
    {
        $this->validate(['chosenShipping' => 'required']);

        $option = $this->shippingOptions->first(fn($option) => $option->getIdentifier() === $this->chosenShipping);

        CartSession::setShippingOption($option);

        $this->refreshCart();

        $this->determineCheckoutStep();
    }

    #[On('cartUpdated')]
    #[On('selectedShippingOption')]
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

        $this->validate(['paymentType' => 'required']);

        if ($this->shippingMethod !== 'send') {
            $this->cart->setShippingAddress($this->cart->billingAddress);

            $shippingMethod = ShippingMethod::find($this->shippingMethod);

            if (! $shippingMethod) {
                $this->dispatch('uncompleted-steps');

                return null;
            }

            $shippingRate = $shippingMethod->shippingRates->first();

            if (! $shippingRate) {
                $this->dispatch('uncompleted-steps');

                return null;
            }

            $shippingOption = $shippingRate->getShippingOption($this->cart);

            $this->cart->setShippingOption($shippingOption);
        }

        $this->cart->meta = [
            'Tipo de pedido' => 'Pedido librería',
            'Método de pago' => __("testa::global.payment_types.{$this->paymentType}.title"),
            'Solicita factura' => $this->wantsInvoice ? __('Sí') : __('No'),
            'Es un regalo' => $this->isGift ? __('Sí') : __('No'),
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
