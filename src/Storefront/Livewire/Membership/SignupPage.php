<?php

namespace Trafikrak\Storefront\Livewire\Membership;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Intervention\Validation\Rules\Iban;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Cart;
use Lunar\Models\CartAddress;
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

    public string $privacy_policy = '';

    public array $paymentTypes = [];

    public ?string $paymentType = null;

    public ?string $directDebitOwnerName = null;
    public ?string $directDebitBankName = null;
    public ?string $directDebitIban = null;

    public function mount(): void
    {
        $this->tiers = MembershipTier::with('plans')->get();
        $this->plans = collect();

        $this->billing->init();

        if (Auth::check()) {
            $this->billing->contact_email = Auth::user()->email;
        }

        $this->paymentTypes = config('trafikrak.payment_types.membership');
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

    public function signup(): RedirectResponse|Redirector
    {
        if (! Auth::check()) {
            return $this->redirectToLogin();
        }

        $rules = collect($this->billing->getRules())
            ->mapWithKeys(fn ($value, $key) => ["billing.$key" => $value])
            ->toArray();

        $this->validate(
            array_merge(
                [
                    'selectedTier' => ['required'],
                    'selectedPlan' => ['required'],
                    'paymentType' => ['required'],
                    'directDebitOwnerName' => ['required_if:paymentType,direct-debit'],
                    'directDebitBankName' => ['required_if:paymentType,direct-debit'],
                    'directDebitIban' => [
                        'required_if:paymentType,direct-debit',
                        'nullable',
                        new Iban(),
                    ],
                    'privacy_policy' => ['accepted', 'required'],
                ],
                $rules,
            ),
        );

        $user = Auth::user();

        $membershipPlan = MembershipPlan::find($this->selectedPlan);

        $meta = [
            'Tipo de pedido' => 'Subscripción socias',
            'Método de pago' => __("trafikrak::global.payment_types.{$this->paymentType}.title"),
        ];

        if ($this->paymentType === 'direct-debit') {
            $meta['Titular de la cuenta'] = $this->directDebitOwnerName;
            $meta['Banco'] = $this->directDebitBankName;
            $meta['IBAN'] = $this->directDebitIban;
        }

        $cart = Cart::create([
            'user_id' => $user->id,
            'currency_id' => StorefrontSession::getCurrency()->id,
            'channel_id' => StorefrontSession::getChannel()->id,
            'meta' => $meta,
        ]);

        $cart->add($membershipPlan);

        $billing = new CartAddress();
        $billing->fill($this->billing->all());
        $cart->setBillingAddress($billing);

        $cart->calculate();

        return redirect()
            ->route(
                'trafikrak.storefront.checkout.process-payment',
                ['id' => $cart->id, 'fingerprint' => $cart->fingerprint(), 'payment' => $this->paymentType],
            );
    }

    public function redirectToLogin(): Redirector|RedirectResponse
    {
        session()->put('url.intended', route('trafikrak.storefront.membership.signup'));

        return redirect()->route('login');
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.membership.signup')
            ->title(__('Asóciate'));
    }
}
