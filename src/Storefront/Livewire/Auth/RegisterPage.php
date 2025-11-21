<?php

namespace Trafikrak\Storefront\Livewire\Auth;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Customer;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;

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
        return view('trafikrak::storefront.livewire.auth.register')
            ->title(__('Regístrate'));
    }

    public function register(): void
    {
        $validated = $this->validate([
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
            'privacy_policy' => ['accepted', 'required'],
        ]);

        self::createUser($validated);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    public static function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        DB::beginTransaction();

        $user = config('auth.providers.users.model')::create($data);

        $customer = Customer::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
        ]);

        $customer->users()->attach($user);
        $customer->customerGroups()->attach(StorefrontSession::getCustomerGroups()->first());

        DB::commit();

        event(new Registered($user));

        Auth::login($user);

        return $user;
    }
}
