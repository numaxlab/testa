<?php

namespace Testa\Storefront\Livewire\Education;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Models\Content\Location;
use Testa\Models\Education\Course;
use Testa\Settings\PaymentSettings;
use Testa\Storefront\Data\CheckoutAddressData;
use Testa\Storefront\Data\CourseRegistrationData;
use Testa\Storefront\Data\RegisterUserData;
use Testa\Storefront\Livewire\Checkout\Forms\AddressForm;
use Testa\Storefront\Queries\Content\GetBannerByLocation;
use Testa\Storefront\Queries\Education\CheckCustomerCourseEnrolment;
use Testa\Storefront\UseCases\Account\RegisterUser;
use Testa\Storefront\UseCases\Education\RegisterForCourse;

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

            if (new CheckCustomerCourseEnrolment()->execute($customer, $this->course)) {
                $this->redirect(route('testa.storefront.education.courses.show', $slug));
                return;
            }
        }

        $this->billing->init();

        if (Auth::check()) {
            $this->billing->contact_email = Auth::user()->email;
        }

        $this->paymentTypes = app(PaymentSettings::class)->education;
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
        $banner = new GetBannerByLocation()->execute(Location::COURSE_REGISTER);

        return view('testa::storefront.livewire.education.course-register', compact('banner'))
            ->title(__('Inscripción en: ') . $this->course->fullTitle);
    }

    public function redirectToLogin(): Redirector|RedirectResponse
    {
        session()->put(
            'url.intended',
            route('testa.storefront.education.courses.register', $this->course->defaultUrl->slug),
        );

        return redirect()->route('login');
    }

    public function register(): Redirector|RedirectResponse
    {
        $rules = [];

        if (!Auth::check()) {
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

        if ($this->invoice) {
            $rules = collect($this->billing->getRules())
                ->mapWithKeys(fn($value, $key) => ["billing.$key" => $value])
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

        if (!Auth::check()) {
            $user = new RegisterUser()->execute(new RegisterUserData(
                first_name: $validated['first_name'],
                last_name: $validated['last_name'],
                email: $validated['email'],
                password: $validated['password'],
            ));
            Auth::login($user);
        }

        $cart = new RegisterForCourse()->execute(
            Auth::user(),
            $this->course,
            new CourseRegistrationData(
                selectedVariantId: $this->selectedVariant,
                paymentType: $this->paymentType,
                invoice: $this->invoice,
                billingAddress: $this->invoice ? CheckoutAddressData::fromForm($this->billing) : null,
            ),
        );

        return redirect()
            ->route(
                'testa.storefront.checkout.process-payment',
                ['id' => $cart->id, 'fingerprint' => $cart->fingerprint(), 'payment' => $this->paymentType],
            );
    }
}
