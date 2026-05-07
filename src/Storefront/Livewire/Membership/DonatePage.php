<?php

namespace Testa\Storefront\Livewire\Membership;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Product;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Settings\PaymentSettings;
use Testa\Settings\TextSettings;
use Testa\Storefront\Data\DonationData;
use Testa\Storefront\Data\RegisterUserData;
use Testa\Storefront\Queries\Membership\GetDonationProduct;
use Testa\Storefront\UseCases\Account\RegisterUser;
use Testa\Storefront\UseCases\Membership\PlaceDonation;

class DonatePage extends Page
{
    public Product $product;

    public array $paymentTypes = [];

    public string $selectedQuantity;

    public ?float $freeQuantityValue;

    public ?string $paymentType = 'card';

    public ?string $privacy_policy;

    public string $directDebitOwnerName = '';
    public string $directDebitBankName = '';
    public string $directDebitIban = '';

    public string $id_number = '';
    public string $comments = '';

    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        $this->product = new GetDonationProduct()->execute();

        $this->paymentTypes = app(PaymentSettings::class)->donation;

        $this->paymentType = $this->paymentTypes[0] ?? null;

        $quantities = $this->quantities;
        if ($quantities->isNotEmpty()) {
            $middleIndex = (int)floor($quantities->count() / 2);
            $this->selectedQuantity = (string)$quantities[$middleIndex]['id'];
        }

        if (Auth::check()) {
            $this->id_number = Auth::user()->latestCustomer()?->tax_identifier ?? '';
        }
    }

    public function getQuantitiesProperty(): Collection
    {
        return $this->product->variants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'pricing' => $variant
                    ->pricing()
                    ->currency(StorefrontSession::getCurrency())
                    ->customerGroups(StorefrontSession::getCustomerGroups())
                    ->get()->matched,
            ];
        })->values();
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.membership.donate', [
            'donateIntro' => app(TextSettings::class)->getDonateIntro(),
        ])->title(__('Donación'));
    }

    public function updatedSelectedQuantity(): void
    {
        $this->resetValidation('selectedQuantity');
    }

    public function donate()
    {
        $isGuest = !Auth::check();

        $rules = [
            'selectedQuantity' => ['required'],
            'freeQuantityValue' => ['required_if:selectedQuantity,free', 'nullable', 'numeric', 'min:1'],
            'paymentType' => ['required'],
            'privacy_policy' => ['accepted', 'required'],
            'id_number' => ['nullable', 'string', 'max:20'],
            'comments' => ['nullable', 'string', 'max:500'],
        ];

        if ($this->paymentType === 'direct-debit') {
            $rules['directDebitOwnerName'] = ['required', 'string', 'max:255'];
            $rules['directDebitBankName'] = ['required', 'string', 'max:255'];
            $rules['directDebitIban'] = ['required', 'string', 'max:34', 'regex:/^[A-Z]{2}\d{2}[A-Z0-9]{4,30}$/'];
        }

        if ($isGuest) {
            $rules = array_merge($rules, [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'string',
                    'lowercase',
                    'email',
                    'max:255',
                    'unique:' . config('auth.providers.users.model'),
                ],
                'password' => ['required', 'string', 'confirmed', Password::defaults()],
            ]);
        }

        $this->validate($rules, [
            'directDebitIban.regex' => __('El IBAN introducido no tiene un formato válido.'),
        ], [
            'email' => 'correo electrónico',
            'password' => 'contraseña',
        ]);

        $guestData = $isGuest ? new RegisterUserData(
            first_name: $this->first_name,
            last_name: $this->last_name,
            email: $this->email,
            password: $this->password,
        ) : null;

        [$donationUser, $cart] = DB::transaction(function () use ($isGuest, $guestData) {
            $user = $isGuest
                ? new RegisterUser()->execute($guestData)
                : Auth::user();

            $cart = new PlaceDonation()->execute(
                $user,
                $this->product,
                new DonationData(
                    selectedQuantity: $this->selectedQuantity,
                    freeQuantityValue: $this->freeQuantityValue ?? null,
                    paymentType: $this->paymentType,
                    idNumber: $this->id_number,
                    comments: $this->comments,
                    directDebitOwnerName: $this->directDebitOwnerName,
                    directDebitBankName: $this->directDebitBankName,
                    directDebitIban: $this->directDebitIban,
                ),
            );

            return [$user, $cart];
        });

        if ($isGuest) {
            Auth::login($donationUser);
        }

        return redirect()
            ->route(
                'testa.storefront.checkout.process-payment',
                ['id' => $cart->id, 'fingerprint' => $cart->fingerprint(), 'payment' => $this->paymentType],
            );
    }

    public function redirectToLogin(): Redirector|RedirectResponse
    {
        session()->put('url.intended', route('testa.storefront.membership.donate'));

        return redirect()->route('login');
    }
}
