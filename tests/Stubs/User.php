<?php

namespace Testa\Tests\Stubs;

use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lunar\Base\Traits\LunarUser;
use NumaxLab\Lunar\Geslib\Traits\LunarGeslibUser;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use LunarUser;
    use LunarGeslibUser;
    use MustVerifyEmail;
    use Notifiable;

    /**
     * Mass-assignable attributes matching the test users table.
     * Privacy policy and other non-column fields are excluded.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'email_verified_at',
        'remember_token',
    ];
}
