<?php

namespace Trafikrak\Storefront\Livewire\Education;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Cart;
use Lunar\Models\CartAddress;
use Lunar\Models\Country;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Models\Content\Banner;
use Trafikrak\Models\Content\Location;
use Trafikrak\Models\Education\Course;
use Trafikrak\Storefront\Livewire\Auth\RegisterPage;
use Trafikrak\Storefront\Livewire\Checkout\Forms\AddressForm;

class CourseRegisterPage extends Page
{
    public Course $course;

    public array $paymentTypes = [];

    public ?string $selectedVariant;

    public bool $invoice = false;

    public AddressForm $billing;

    public ?string $paymentType = null;

    public string $privacy_policy = '';

    public function mount($slug): void
    {
        $this->fetchUrl(
            slug: $slug,
            type: (new Course)->getMorphClass(),
            firstOrFail: true,
            eagerLoad: [
                'element.topic',
                'element.purchasable',
                'element.purchasable.variants',
                'element.purchasable.variants.values',
            ],
        );

        $this->course = $this->url->element;

        if (Auth::check()) {
            $customer = Auth::user()->latestCustomer();

            if ($customer->courses->contains($this->course)) {
                redirect()->route('trafikrak.storefront.education.courses.show', $slug);
                return;
            }
        }

        $this->billing->init();

        if (Auth::check()) {
            $this->billing->contact_email = Auth::user()->email;
        }

        $this->paymentTypes = config('trafikrak.payment_types.education');
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

    public function render(): View
    {
        $banner = Banner::whereJsonContains('locations', Location::COURSE_REGISTER->value)
            ->where('is_published', true)
            ->first();

        return view('trafikrak::storefront.livewire.education.course-register', compact('banner'));
    }

    public function redirectToLogin(): Redirector|RedirectResponse
    {
        session()->put(
            'url.intended',
            route('trafikrak.storefront.education.courses.register', $this->course->defaultUrl->slug),
        );

        return redirect()->route('login');
    }

    public function register(): Redirector|RedirectResponse
    {
        $rules = [];

        if (! Auth::check()) {
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

        if ($this->invoice) {
            $rules = collect($this->billing->getRules())
                ->mapWithKeys(fn ($value, $key) => ["billing.$key" => $value])
                ->toArray();
        }

        $validated = $this->validate(
            array_merge(
                [
                    'selectedVariant' => ['required'],
                    'paymentType' => ['required'],
                    'privacy_policy' => ['accepted', 'required'],
                ],
                $rules,
            ),
        );

        if (! Auth::check()) {
            $user = RegisterPage::createUser(
                Arr::only($validated, ['first_name', 'last_name', 'email', 'password']),
            );
        } else {
            $user = Auth::user();
        }

        $cart = Cart::create([
            'user_id' => $user->id,
            'customer_id' => $user->latestCustomer()?->id,
            'currency_id' => StorefrontSession::getCurrency()->id,
            'channel_id' => StorefrontSession::getChannel()->id,
            'meta' => [
                'Factura' => $this->invoice ? 'Si' : 'No',
                'Tipo de pedido' => 'Curso',
                'Método de pago' => __("trafikrak::global.payment_types.{$this->paymentType}.title"),
            ],
        ]);

        foreach ($this->course->purchasable->variants as $variant) {
            if ($variant->id == $this->selectedVariant) {
                $cart->add($variant);
                break;
            }
        }

        $billing = new CartAddress();

        if ($this->invoice) {
            $billing->fill($this->billing->all());
        } else {
            $billing->first_name = $user->latestCustomer()->first_name;
            $billing->country_id = Country::where('iso2', config('trafikrak.default_billing_address.country_iso2'))
                ->firstOrFail()->id;
            $billing->city = config('trafikrak.default_billing_address.city');
            $billing->postcode = config('trafikrak.default_billing_address.postcode');
            $billing->line_one = config('trafikrak.default_billing_address.line_one');
        }

        $cart->setBillingAddress($billing);

        $cart->calculate();

        return redirect()
            ->route(
                'trafikrak.storefront.checkout.process-payment',
                ['id' => $cart->id, 'fingerprint' => $cart->fingerprint(), 'payment' => $this->paymentType],
            );
    }
}
