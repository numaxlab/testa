<?php

namespace Testa\Storefront\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Storefront\Data\RegisterUserData;
use Testa\Storefront\UseCases\Account\RegisterUser;

class RegisterPage extends Page
{
    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $privacy_policy = '';

    public function render(): View
    {
        return view('testa::storefront.livewire.auth.register')
            ->title(__('Regístrate'));
    }

    public function register(): void
    {
        $this->validate([
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
            'privacy_policy' => ['accepted', 'required'],
        ]);

        $user = new RegisterUser()->execute(new RegisterUserData(
            first_name: $this->first_name,
            last_name: $this->last_name,
            email: $this->email,
            password: $this->password,
        ));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}
