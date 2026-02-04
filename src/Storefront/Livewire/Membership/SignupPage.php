<?php

namespace Testa\Storefront\Livewire\Membership;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Intervention\Validation\Rules\Iban;
use Livewire\Attributes\Url;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Cart;
use Lunar\Models\CartAddress;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Models\Membership\MembershipPlan;
use Testa\Models\Membership\MembershipTier;
use Testa\Storefront\Livewire\Auth\RegisterPage;
use Testa\Storefront\Livewire\Checkout\Forms\AddressForm;

class SignupPage extends Page
{
    public Collection $tiers;

    public Collection $plans;

    #[Url]
    public ?string $selectedTier = null;

    #[Url]
    public ?string $selectedPlan = null;

    public AddressForm $billing;

    public string $privacy_policy = '';

    public array $paymentTypes = [];

    public ?string $paymentType = null;

    public ?string $directDebitOwnerName = null;
    public ?string $directDebitBankName = null;
    public ?string $directDebitIban = null;

    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        $this->tiers = MembershipTier::where('is_published', true)->get();
        $this->retrieveTierPlans();

        $this->billing->init();

        if (Auth::check()) {
            $this->billing->contact_email = Auth::user()->email;
        }

        $this->paymentTypes = config('testa.payment_types.membership');
    }

    private function retrieveTierPlans(): void
    {
        if ($this->selectedTier === null) {
            $this->plans = collect();
        }

        $this->plans = MembershipPlan::where('membership_tier_id', $this->selectedTier)
            ->with(['variant'])
            ->where('is_published', true)
            ->get();
    }

    public function updated($field, $value): void
    {
        if ($field === 'selectedTier') {
            $this->retrieveTierPlans();

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
        $isGuest = ! Auth::check();

        $rules = collect($this->billing->getRules())
            ->mapWithKeys(fn($value, $key) => ["billing.$key" => $value])
            ->toArray();

        // For guests, name and email fields come from registration form, not billing
        if ($isGuest) {
            unset($rules['billing.first_name'], $rules['billing.last_name'], $rules['billing.contact_email']);
        }

        $baseRules = [
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
        ];

        if ($isGuest) {
            $registrationRules = [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'string',
                    'lowercase',
                    'email',
                    'max:255',
                    'unique:'.config('auth.providers.users.model'),
                ],
                'password' => ['required', 'string', 'confirmed', Password::defaults()],
            ];

            $baseRules = array_merge($baseRules, $registrationRules);
        }

        $this->validate(array_merge($baseRules, $rules));

        if ($isGuest) {
            RegisterPage::createUser([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => $this->password,
            ]);

            // Copy registration data to billing address
            $this->billing->first_name = $this->first_name;
            $this->billing->last_name = $this->last_name;
            $this->billing->contact_email = $this->email;
        }

        $user = Auth::user();

        $membershipPlan = MembershipPlan::find($this->selectedPlan);

        $meta = [
            'Tipo de pedido' => 'Subscripción socias',
            'Método de pago' => __("testa::global.payment_types.{$this->paymentType}.title"),
        ];

        if ($this->paymentType === 'direct-debit') {
            $meta['Titular de la cuenta'] = $this->directDebitOwnerName;
            $meta['Banco'] = $this->directDebitBankName;
            $meta['IBAN'] = $this->directDebitIban;
        }

        $cart = Cart::create([
            'user_id' => $user->id,
            'customer_id' => $user->latestCustomer()?->id,
            'currency_id' => StorefrontSession::getCurrency()->id,
            'channel_id' => StorefrontSession::getChannel()->id,
            'meta' => $meta,
        ]);

        $cart->add($membershipPlan->variant);

        $billing = new CartAddress();
        $billing->fill($this->billing->all());
        $cart->setBillingAddress($billing);

        $cart->calculate();

        return redirect()
            ->route(
                'testa.storefront.checkout.process-payment',
                ['id' => $cart->id, 'fingerprint' => $cart->fingerprint(), 'payment' => $this->paymentType],
            );
    }

    public function redirectToLogin(): Redirector|RedirectResponse
    {
        session()->put('url.intended', route('testa.storefront.membership.signup'));

        return redirect()->route('login');
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.membership.signup')
            ->title(__('Asóciate'));
    }
}
