<?php

namespace Testa\Storefront\Livewire\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Storefront\UseCases\Account\UpdateUserPassword;

class PasswordPage extends Page
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function render(): View
    {
        return view('testa::storefront.livewire.account.password');
    }

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', PasswordRule::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        new UpdateUserPassword()->execute(Auth::user(), $validated['password']);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}
