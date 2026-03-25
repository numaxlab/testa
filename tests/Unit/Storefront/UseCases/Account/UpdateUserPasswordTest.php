<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Testa\Storefront\UseCases\Account\UpdateUserPassword;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Schema::table('users', function ($table) {
        $table->dropColumn('name');
        $table->string('first_name')->after('id');
        $table->string('last_name')->after('first_name');
    });

    config(['auth.providers.users.model' => \Testa\Tests\Stubs\User::class]);

    $userModel = config('auth.providers.users.model');
    $userModel::unguard();
    $this->user = $userModel::create([
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => Hash::make('old-password'),
    ]);
    $userModel::reguard();
});

it('updates the user password', function () {
    new UpdateUserPassword()->execute($this->user, 'new-password');

    $this->user->refresh();
    expect(Hash::check('new-password', $this->user->password))->toBeTrue();
});

it('stores the password hashed, not in plain text', function () {
    new UpdateUserPassword()->execute($this->user, 'new-password');

    $this->user->refresh();
    expect($this->user->password)->not->toBe('new-password');
});
