<?php

namespace Testa\Storefront\Livewire\Checkout\Forms;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Lunar\Models\Address;
use Lunar\Models\Country;
use Lunar\Models\Customer;
use Lunar\Models\State;

class AddressForm extends Form
{
    public Collection $countries;

    public Collection $states;

    public Collection $customerAddresses;

    public bool $saveToUser = false;

    public ?int $customer_address_id = null;

    #[Validate('required|string|max:255')]
    public string $first_name = '';

    #[Validate('required|string|max:255')]
    public string $last_name = '';

    #[Validate('nullable|string|max:255')]
    public ?string $company_name = null;

    #[Validate('nullable|string|max:20')]
    public ?string $contact_phone = null;

    #[Validate('required|email|max:255')]
    public string $contact_email = '';

    #[Validate('required')]
    public ?int $country_id = null;

    #[Validate('required')]
    public ?string $state = null;

    #[Validate('required|string|max:20')]
    public string $postcode = '';

    #[Validate('required|string|max:255')]
    public string $city = '';

    #[Validate('required|string|max:255')]
    public string $line_one = '';

    #[Validate('nullable|string|max:255')]
    public ?string $line_two = null;

    public function init(): void
    {
        $this->customerAddresses = collect();

        $user = Auth::user();

        if ($user) {
            /** @var Customer $customer */
            $customer = $user->latestCustomer();

            if ($customer->addresses->isNotEmpty()) {
                $this->customerAddresses = $customer->addresses;
            }
        }

        $this->loadCountries();
    }

    public function loadCountries(): void
    {
        $this->countries = Country::orderBy('native')->get();
        $this->states = collect();
    }

    public function loadAddress(?int $customerAddressId = null): void
    {
        $address = $this->customerAddresses->firstWhere('id', $customerAddressId);

        if (! $address) {
            return;
        }

        $this->first_name = $address->first_name;
        $this->last_name = $address->last_name;
        $this->company_name = $address->company_name;
        $this->country_id = $address->country_id;

        if ($this->country_id !== null) {
            $this->loadStates($this->country_id);
        }

        $this->state = $address->state;
        $this->postcode = $address->postcode;
        $this->city = $address->city;
        $this->line_one = $address->line_one;
        $this->line_two = $address->line_two;
    }

    public function loadStates(?int $countryId = null): void
    {
        $this->state = null;

        if ($countryId === null) {
            $this->states = collect();
        } else {
            $this->states = State::where('country_id', $countryId)
                ->orderBy('name')
                ->get();
        }
    }

    public function store(): void
    {
        $validated = $this->validate();

        $validated['customer_id'] = Auth::user()->latestCustomer()?->id;

        Address::create($validated);
    }
}