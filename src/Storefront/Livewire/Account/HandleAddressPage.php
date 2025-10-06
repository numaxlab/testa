<?php

namespace Trafikrak\Storefront\Livewire\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Storefront\Livewire\Account\Forms\AddressForm;

class HandleAddressPage extends Page
{
    public AddressForm $form;

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.account.handle-address');
    }

    public function mount($id = null): void
    {
        $user = Auth::user();
        $customer = $user->latestCustomer();

        $this->form->loadCountries();

        if ($id !== null) {
            $this->form->setAddress($customer->addresses()->findOrFail($id));

            return;
        }

        $this->form->first_name = $user->name;
        $this->form->last_name = $user->last_name;
        $this->form->company_name = $user->latestCustomer()?->company_name;
    }

    public function save(): void
    {
        $this->form->store(Auth::user()->latestCustomer()?->id);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    public function updated($field, $value): void
    {
        if ($field === 'form.country_id') {
            $this->form->loadStates($value);
        }
    }
}