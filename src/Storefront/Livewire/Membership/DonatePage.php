<?php

namespace Testa\Storefront\Livewire\Membership;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Cart;
use Lunar\Models\CartAddress;
use Lunar\Models\Country;
use Lunar\Models\Product;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Settings\ContactSettings;
use Testa\Settings\PaymentSettings;
use Testa\Storefront\Livewire\Auth\RegisterPage;

class DonatePage extends Page
{
    public const string DONATION_PRODUCT_SKU = 'donation';

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
        $this->product = Product::whereHas('variants', function ($query) {
            $query->where('sku', self::DONATION_PRODUCT_SKU);
        })->with([
            'variants.basePrices.currency',
            'variants.basePrices.priceable',
            'variants.values.option',
        ])->firstOrFail();

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

    public function donate(ContactSettings $contactSettings)
    {
        $isGuest = ! Auth::check();

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
                    'unique:'.config('auth.providers.users.model'),
                ],
                'password' => ['required', 'string', 'confirmed', Password::defaults()],
            ]);
        }

        $this->validate($rules);

        if ($isGuest) {
            RegisterPage::createUser([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => $this->password,
            ]);
        }

        $user = Auth::user();

        $cart = Cart::create([
            'user_id' => $user->id,
            'customer_id' => $user->latestCustomer()?->id,
            'currency_id' => StorefrontSession::getCurrency()->id,
            'channel_id' => StorefrontSession::getChannel()->id,
            'meta' => [
                'Tipo de pedido' => 'Donación',
                'Método de pago' => __("testa::global.payment_types.{$this->paymentType}.title"),
                'DNI/NIF' => $this->id_number,
                'Comentarios' => $this->comments,
            ],
        ]);

        if ($this->selectedQuantity === 'free') {
            $variant = $this->product->variants->firstWhere('sku', self::DONATION_PRODUCT_SKU);
            $unitPriceInCents = (int) ($this->freeQuantityValue * 100);
            $cart->add($variant, 1, ['unit_price' => $unitPriceInCents]);
        } else {
            $variant = $this->product->variants->find($this->selectedQuantity);
            $cart->add($variant);
        }

        $billing = new CartAddress();
        $billing->first_name = $user->latestCustomer()->first_name;
        $primaryAddress = $contactSettings->getPrimaryAddress();
        $billing->country_id = Country::where('iso2', $primaryAddress['country_iso2'])
            ->firstOrFail()->id;
        $billing->city = $primaryAddress['city'];
        $billing->postcode = $primaryAddress['postcode'];
        $billing->line_one = $primaryAddress['line_one'];
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
        session()->put('url.intended', route('testa.storefront.membership.donate'));

        return redirect()->route('login');
    }
}
