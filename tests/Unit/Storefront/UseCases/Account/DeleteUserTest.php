<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Testa\Storefront\UseCases\Account\DeleteUser;
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
        'password' => bcrypt('password'),
    ]);
    $userModel::reguard();
});

it('deletes the user', function () {
    $userModel = config('auth.providers.users.model');

    new DeleteUser()->execute($this->user);

    expect($userModel::find($this->user->id))->toBeNull();
});

it('does not delete other users', function () {
    $userModel = config('auth.providers.users.model');
    $userModel::unguard();
    $otherUser = $userModel::create([
        'first_name' => 'Other',
        'last_name' => 'User',
        'email' => 'other@example.com',
        'password' => bcrypt('password'),
    ]);
    $userModel::reguard();

    new DeleteUser()->execute($this->user);

    expect($userModel::find($otherUser->id))->not->toBeNull();
});
