<?php

namespace Testa\Storefront\UseCases\Account;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Customer;
use Testa\Storefront\Data\RegisterUserData;

final class RegisterUser
{
    public function execute(RegisterUserData $data): Authenticatable
    {
        DB::beginTransaction();

        $user = config('auth.providers.users.model')::create([
            'first_name' => $data->first_name,
            'last_name' => $data->last_name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
        ]);

        $customer = Customer::create([
            'first_name' => $data->first_name,
            'last_name' => $data->last_name,
        ]);

        $customer->users()->attach($user);
        $customer->customerGroups()->attach(StorefrontSession::getCustomerGroups()->first());

        DB::commit();

        event(new Registered($user));

        Auth::login($user);

        return $user;
    }
}
