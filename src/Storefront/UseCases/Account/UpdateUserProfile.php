<?php

namespace Testa\Storefront\UseCases\Account;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Testa\Models\Customer;
use Testa\Storefront\Data\UpdateProfileData;

final class UpdateUserProfile
{
    public function execute(Authenticatable $user, Customer $customer, UpdateProfileData $data): void
    {
        DB::beginTransaction();

        $user->fill([
            'first_name' => $data->first_name,
            'last_name' => $data->last_name,
            'email' => $data->email,
        ]);

        $customer->update([
            'first_name' => $data->first_name,
            'last_name' => $data->last_name,
            'tax_identifier' => $data->tax_identifier,
            'company_name' => $data->company_name,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        DB::commit();
    }
}
