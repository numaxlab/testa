<?php

namespace Testa\Tests\Stubs;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Lunar\Base\Traits\LunarUser;

class User extends Authenticatable
{
    use LunarUser;

    protected $guarded = [];
}
