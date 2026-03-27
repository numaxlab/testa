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
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Settings\PaymentSettings;
use Testa\Settings\TextSettings;
use Testa\Storefront\Data\CheckoutAddressData;
use Testa\Storefront\Data\MembershipSignupData;
use Testa\Storefront\Data\RegisterUserData;
use Testa\Storefront\Livewire\Checkout\Forms\AddressForm;
use Testa\Storefront\Queries\Membership\GetMembershipPlansByTier;
use Testa\Storefront\Queries\Membership\GetPublishedMembershipTiers;
use Testa\Storefront\UseCases\Account\RegisterUser;
use Testa\Storefront\UseCases\Membership\SignupMembership;

class SignupPage extends Page
{
    public Collection $tiers;

    public Collection $plans;

    public string $membershipIntro = '';

    public string $membershipOptionsDescription = '';

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

    public string $id_number = '';

    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        $this->tiers = new GetPublishedMembershipTiers()->execute();
        $this->retrieveTierPlans();

        $textSettings = app(TextSettings::class);
        $this->membershipIntro = $textSettings->getMembershipIntro();
        $this->membershipOptionsDescription = $textSettings->getMembershipOptionsDescription();

        $this->billing->init();

        if (Auth::check()) {
            $user = Auth::user();
            $customer = $user->latestCustomer();

            $this->billing->contact_email = $user->email;

            if ($customer) {
                $this->billing->first_name = $customer->first_name ?? '';
                $this->billing->last_name = $customer->last_name ?? '';
            }
        }

        $this->paymentTypes = app(PaymentSettings::class)->membership;
    }

    private function retrieveTierPlans(): void
    {
        if ($this->selectedTier === null) {
            $this->plans = collect();

            return;
        }

        $this->plans = new GetMembershipPlansByTier()->execute($this->selectedTier);
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
            'id_number' => ['nullable', 'string', 'max:20'],
        ];

        if ($isGuest) {
            $baseRules = array_merge($baseRules, [
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
            ]);
        }

        $this->validate(array_merge($baseRules, $rules));

        if ($isGuest) {
            new RegisterUser()->execute(new RegisterUserData(
                first_name: $this->first_name,
                last_name: $this->last_name,
                email: $this->email,
                password: $this->password,
            ));

            $this->billing->first_name = $this->first_name;
            $this->billing->last_name = $this->last_name;
            $this->billing->contact_email = $this->email;
        }

        $cart = new SignupMembership()->execute(
            Auth::user(),
            new MembershipSignupData(
                membershipPlanId: $this->selectedPlan,
                paymentType: $this->paymentType,
                idNumber: $this->id_number,
                directDebitOwnerName: $this->directDebitOwnerName,
                directDebitBankName: $this->directDebitBankName,
                directDebitIban: $this->directDebitIban,
                billingAddress: CheckoutAddressData::fromForm($this->billing),
            ),
        );

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
