<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Lunar\Models\Currency;
use Lunar\Models\Customer as LunarCustomer;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Models\Customer;
use Testa\Storefront\Data\UpdateProfileData;
use Testa\Storefront\UseCases\Account\UpdateUserProfile;
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

    $userModel = config('auth.providers.users.model');
    $userModel::unguard();
    $this->user = $userModel::create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]);
    $userModel::reguard();

    $lunarCustomer = LunarCustomer::create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'company_name' => 'ACME',
        'tax_identifier' => 'B12345678',
    ]);
    $lunarCustomer->users()->attach($this->user);
    $this->customer = Customer::find($lunarCustomer->id);
});

function makeProfileData(array $overrides = []): UpdateProfileData
{
    return new UpdateProfileData(
        first_name: $overrides['first_name'] ?? 'John',
        last_name: $overrides['last_name'] ?? 'Doe',
        email: $overrides['email'] ?? 'john@example.com',
        tax_identifier: $overrides['tax_identifier'] ?? null,
        company_name: $overrides['company_name'] ?? null,
    );
}

it('updates the user first_name and last_name', function () {
    $data = makeProfileData(['first_name' => 'Jane', 'last_name' => 'Smith']);

    new UpdateUserProfile()->execute($this->user, $this->customer, $data);

    $this->user->refresh();
    expect($this->user->first_name)
        ->toBe('Jane')
        ->and($this->user->last_name)->toBe('Smith');
});

it('updates the user email', function () {
    $data = makeProfileData(['email' => 'new@example.com']);

    new UpdateUserProfile()->execute($this->user, $this->customer, $data);

    $this->user->refresh();
    expect($this->user->email)->toBe('new@example.com');
});

it('clears email_verified_at when email changes', function () {
    $data = makeProfileData(['email' => 'new@example.com']);

    new UpdateUserProfile()->execute($this->user, $this->customer, $data);

    $this->user->refresh();
    expect($this->user->email_verified_at)->toBeNull();
});

it('does not clear email_verified_at when email is unchanged', function () {
    $data = makeProfileData(['email' => 'john@example.com']);

    new UpdateUserProfile()->execute($this->user, $this->customer, $data);

    $this->user->refresh();
    expect($this->user->email_verified_at)->not->toBeNull();
});

it('updates the customer tax_identifier and company_name', function () {
    $data = makeProfileData(['tax_identifier' => 'C99999999', 'company_name' => 'New Corp']);

    new UpdateUserProfile()->execute($this->user, $this->customer, $data);

    $this->customer->refresh();
    expect($this->customer->tax_identifier)
        ->toBe('C99999999')
        ->and($this->customer->company_name)->toBe('New Corp');
});

it('updates the customer first_name and last_name', function () {
    $data = makeProfileData(['first_name' => 'Jane', 'last_name' => 'Smith']);

    new UpdateUserProfile()->execute($this->user, $this->customer, $data);

    $this->customer->refresh();
    expect($this->customer->first_name)
        ->toBe('Jane')
        ->and($this->customer->last_name)->toBe('Smith');
});
