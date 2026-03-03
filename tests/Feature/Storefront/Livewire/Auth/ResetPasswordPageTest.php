<?php

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Storefront\Livewire\Auth\ResetPasswordPage;

use function Pest\Livewire\livewire;

beforeEach(function () {
    Schema::table('users', function ($table) {
        $table->dropColumn('name');
        $table->string('first_name')->after('id');
        $table->string('last_name')->after('first_name');
    });

    config(['auth.providers.users.model' => \Testa\Tests\Stubs\User::class]);

    $this->language = Language::factory()->create(['default' => true]);
    $this->currency = Currency::factory()->create(['default' => true, 'decimal_places' => 2]);
    $this->channel = Channel::factory()->create(['default' => true]);
    $this->taxClass = TaxClass::factory()->create(['default' => true]);
    $this->customerGroup = CustomerGroup::factory()->create(['default' => true]);

    $userModel = config('auth.providers.users.model');
    $userModel::unguard();
    $this->user = $userModel::create([
        'first_name' => 'Reset',
        'last_name' => 'User',
        'email' => 'reset@example.com',
        'password' => bcrypt('oldpassword'),
    ]);
    $userModel::reguard();
});

describe('mount', function () {
    it('sets token from route parameter', function () {
        $component = livewire(ResetPasswordPage::class, ['token' => 'test-token-123']);

        expect($component->get('token'))->toBe('test-token-123');
    });
});

describe('validation', function () {
    it('validates email is required', function () {
        livewire(ResetPasswordPage::class, ['token' => 'some-token'])
            ->set('email', '')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('resetPassword')
            ->assertHasErrors(['email']);
    });

    it('validates password is required', function () {
        livewire(ResetPasswordPage::class, ['token' => 'some-token'])
            ->set('email', 'reset@example.com')
            ->set('password', '')
            ->set('password_confirmation', '')
            ->call('resetPassword')
            ->assertHasErrors(['password']);
    });

    it('validates password confirmation must match', function () {
        livewire(ResetPasswordPage::class, ['token' => 'some-token'])
            ->set('email', 'reset@example.com')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'different')
            ->call('resetPassword')
            ->assertHasErrors(['password']);
    });
});

describe('resetPassword', function () {
    it('adds email error when token is invalid', function () {
        livewire(ResetPasswordPage::class, ['token' => 'invalid-token'])
            ->set('email', 'reset@example.com')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('resetPassword')
            ->assertHasErrors(['email']);
    });

    it('updates password and redirects to login when token is valid', function () {
        $token = Password::createToken($this->user);

        livewire(ResetPasswordPage::class, ['token' => $token])
            ->set('email', 'reset@example.com')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('resetPassword')
            ->assertHasNoErrors()
            ->assertRedirect(route('login'));
    });
});
