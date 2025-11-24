<?php

namespace Trafikrak\Storefront\Livewire\Membership;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Cart;
use Lunar\Models\CartAddress;
use Lunar\Models\Product;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Storefront\Livewire\Checkout\Forms\AddressForm;

class DonatePage extends Page
{
    public const string DONATION_PRODUCT_SKU = 'donation';

    public Product $product;

    public array $paymentTypes = [];

    public string $selectedQuantity;

    public ?float $freeQuantityValue;

    public AddressForm $billing;

    public ?string $paymentType = 'card';

    public ?string $privacy_policy;

    public function mount(): void
    {
        $this->product = Product::whereHas('variants', function ($query) {
            $query->where('sku', self::DONATION_PRODUCT_SKU);
        })->with([
            'variants.basePrices.currency',
            'variants.basePrices.priceable',
            'variants.values.option',
        ])->firstOrFail();

        $this->billing->init();

        if (Auth::check()) {
            $this->billing->contact_email = Auth::user()->email;
        }

        $this->paymentTypes = config('trafikrak.payment_types.donation');
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
        return view('trafikrak::storefront.livewire.membership.donate')
            ->title(__('Donación'));
    }

    public function donate()
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
                    'selectedQuantity' => ['required'],
                    'freeQuantityValue' => ['required_if:selectedQuantity,free', 'nullable', 'numeric', 'min:1'],
                    'paymentType' => ['required'],
                    'privacy_policy' => ['accepted', 'required'],
                ],
                $rules,
            ),
        );

        $user = Auth::user();

        $cart = Cart::create([
            'user_id' => $user->id,
            'customer_id' => $user->latestCustomer()?->id,
            'currency_id' => StorefrontSession::getCurrency()->id,
            'channel_id' => StorefrontSession::getChannel()->id,
            'meta' => [
                'Tipo de pedido' => 'Donación',
                'Método de pago' => __("trafikrak::global.payment_types.{$this->paymentType}.title"),
            ],
        ]);

        if ($this->selectedQuantity === 'free') {
            $variant = $this->product->variants->firstWhere('sku', self::DONATION_PRODUCT_SKU);
        } else {
            $variant = $this->product->variants->find($this->selectedQuantity);
        }

        $cart->add($variant);

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
        session()->put('url.intended', route('trafikrak.storefront.membership.donate'));

        return redirect()->route('login');
    }

    public function updated($field, $value): void
    {
        if ($field === 'billing.customer_address_id') {
            $this->billing->loadAddress($value);
        }
        if ($field === 'billing.country_id') {
            $this->billing->loadStates($value);
        }
    }
}
