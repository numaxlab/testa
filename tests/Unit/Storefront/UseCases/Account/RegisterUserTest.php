<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Storefront\Data\RegisterUserData;
use Testa\Storefront\UseCases\Account\RegisterUser;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Schema::table('users', function ($table) {
        $table->dropColumn('name');
        $table->string('first_name')->after('id');
        $table->string('last_name')->after('first_name');
    });

    config(['auth.providers.users.model' => \Testa\Tests\Stubs\User::class]);

    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);
    Channel::factory()->create(['default' => true]);
    CustomerGroup::factory()->create(['default' => true]);
});

function makeRegisterUserData(array $overrides = []): RegisterUserData
{
    return new RegisterUserData(
        first_name: $overrides['first_name'] ?? 'Ana',
        last_name: $overrides['last_name'] ?? 'García',
        email: $overrides['email'] ?? 'ana@example.com',
        password: $overrides['password'] ?? 'secret123',
    );
}

it('creates a user in the database', function () {
    $data = makeRegisterUserData();

    new RegisterUser()->execute($data);

    expect(config('auth.providers.users.model')::where('email', 'ana@example.com')->exists())->toBeTrue();
});

it('creates a customer linked to the user', function () {
    $data = makeRegisterUserData();

    $user = new RegisterUser()->execute($data);

    expect($user->customers()->count())->toBe(1);
    $customer = $user->customers()->first();
    expect($customer->first_name)->toBe('Ana');
    expect($customer->last_name)->toBe('García');
});

it('hashes the password', function () {
    $data = makeRegisterUserData(['password' => 'plainpassword']);

    $user = new RegisterUser()->execute($data);

    expect($user->password)->not->toBe('plainpassword');
    expect(\Illuminate\Support\Facades\Hash::check('plainpassword', $user->password))->toBeTrue();
});

it('returns the created user', function () {
    $data = makeRegisterUserData(['email' => 'test@example.com']);

    $user = new RegisterUser()->execute($data);

    expect($user->email)->toBe('test@example.com');
});
