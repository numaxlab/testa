<?php

namespace Testa\Storefront\Livewire\Membership;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Product;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Settings\PaymentSettings;
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
        return view('testa::storefront.livewire.membership.donate')
            ->title(__('Donación'));
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

        $this->validate($rules);

        if ($isGuest) {
            $user = new RegisterUser()->execute(new RegisterUserData(
                first_name: $this->first_name,
                last_name: $this->last_name,
                email: $this->email,
                password: $this->password,
            ));
            Auth::login($user);
        }

        $cart = new PlaceDonation()->execute(
            Auth::user(),
            $this->product,
            new DonationData(
                selectedQuantity: $this->selectedQuantity,
                freeQuantityValue: $this->freeQuantityValue ?? null,
                paymentType: $this->paymentType,
                idNumber: $this->id_number,
                comments: $this->comments,
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
        session()->put('url.intended', route('testa.storefront.membership.donate'));

        return redirect()->route('login');
    }
}
