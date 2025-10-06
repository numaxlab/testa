<?php

namespace Trafikrak\Storefront\Livewire\Membership;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Lunar\Facades\Payments;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Cart;
use Lunar\Models\CartAddress;
use Lunar\Models\Order;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Models\Membership\MembershipPlan;
use Trafikrak\Models\Membership\MembershipTier;
use Trafikrak\Storefront\Livewire\Checkout\Forms\AddressForm;

class SignupPage extends Page
{
    public Collection $tiers;
    public ?string $selectedTier = null;
    public Collection $plans;
    public ?string $selectedPlan = null;

    public AddressForm $billing;

    public function mount(): void
    {
        $this->tiers = MembershipTier::with('plans')->get();
        $this->plans = collect();

        $this->billing->init();
    }

    public function updated($field, $value): void
    {
        if ($field === 'selectedTier') {
            $tier = $this->tiers->firstWhere('id', $value);

            $this->plans = $tier ? $tier->plans : collect();

            $this->selectedPlan = null;
        }
        if ($field === 'billing.customer_address_id') {
            $this->billing->loadAddress($value);
        }
        if ($field === 'billing.country_id') {
            $this->billing->loadStates($value);
        }
    }

    public function signup()
    {
        // 1. Comprobar login
        // 2. Validar datos

        $user = Auth::user();

        $membershipPlan = MembershipPlan::find($this->selectedPlan);

        $cart = Cart::create([
            'user_id' => $user->id,
            'currency_id' => StorefrontSession::getCurrency()->id,
            'channel_id' => StorefrontSession::getChannel()->id,
            'meta' => [
                'subscription_purchase' => true,
            ],
        ]);

        $cart->add($membershipPlan);

        $billing = new CartAddress();
        $billing->fill($this->billing->all());
        $cart->setBillingAddress($billing);

        $cart->calculate();

        $paymentDriver = Payments::driver('cash-in-hand')
            ->cart($cart)
            ->withData([
                'payment_intent_client_secret' => null,
                'payment_intent' => null,
            ]);

        $payment = $paymentDriver->authorize();

        if ($payment->success) {
            $order = Order::findOrFail($payment->orderId);

            return redirect()->route('dashboard', $order->fingerprint);
        }
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.membership.signup');
    }
}
