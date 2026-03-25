<?php

namespace Testa\Storefront\Livewire\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Actions\Logout;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Storefront\Data\UpdateProfileData;
use Testa\Storefront\UseCases\Account\DeleteUser;
use Testa\Storefront\UseCases\Account\UpdateUserProfile;

class ProfilePage extends Page
{
    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public ?string $tax_identifier;

    public ?string $company_name;

    public string $password = '';

    public function render(): View
    {
        return view('testa::storefront.livewire.account.profile');
    }

    public function mount(): void
    {
        $user = Auth::user();
        $this->first_name = $user->name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->tax_identifier = $user->latestCustomer()?->tax_identifier;
        $this->company_name = $user->latestCustomer()?->company_name;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:255'],

            'last_name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(config('auth.providers.users.model'))->ignore($user->id),
            ],

            'tax_identifier' => ['nullable', 'string', 'max:255'],

            'company_name' => ['nullable', 'string', 'max:255'],
        ]);

        $data = new UpdateProfileData(
            first_name: $validated['first_name'],
            last_name: $validated['last_name'],
            email: $validated['email'],
            tax_identifier: $validated['tax_identifier'],
            company_name: $validated['company_name'],
        );

        new UpdateUserProfile()->execute($user, $user->latestCustomer(), $data);

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        $user = Auth::user();
        $logout();
        new DeleteUser()->execute($user);

        $this->redirect('/', navigate: true);
    }
}
