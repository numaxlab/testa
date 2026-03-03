<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Storefront\Livewire\Account\PasswordPage;

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
        'first_name' => 'Pass',
        'last_name' => 'User',
        'email' => 'pass@example.com',
        'password' => bcrypt('currentpassword'),
    ]);
    $userModel::reguard();
});

describe('validation', function () {
    it('validates current_password is required', function () {
        $this->actingAs($this->user);

        livewire(PasswordPage::class)
            ->set('current_password', '')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('updatePassword')
            ->assertHasErrors(['current_password']);
    });

    it('validates new password is required', function () {
        $this->actingAs($this->user);

        livewire(PasswordPage::class)
            ->set('current_password', 'currentpassword')
            ->set('password', '')
            ->set('password_confirmation', '')
            ->call('updatePassword')
            ->assertHasErrors(['password']);
    });

    it('validates new password confirmation must match', function () {
        $this->actingAs($this->user);

        livewire(PasswordPage::class)
            ->set('current_password', 'currentpassword')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'different')
            ->call('updatePassword')
            ->assertHasErrors(['password']);
    });

    it('validates current_password matches actual password', function () {
        $this->actingAs($this->user);

        livewire(PasswordPage::class)
            ->set('current_password', 'wrongpassword')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('updatePassword')
            ->assertHasErrors(['current_password']);
    });
});

describe('updatePassword', function () {
    it('updates the password in the database', function () {
        $this->actingAs($this->user);

        livewire(PasswordPage::class)
            ->set('current_password', 'currentpassword')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('updatePassword')
            ->assertHasNoErrors();

        $this->user->refresh();
        expect(Hash::check('newpassword123', $this->user->password))->toBeTrue();
    });

    it('resets form fields after successful update', function () {
        $this->actingAs($this->user);

        $component = livewire(PasswordPage::class)
            ->set('current_password', 'currentpassword')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('updatePassword');

        expect($component->get('current_password'))->toBe('');
        expect($component->get('password'))->toBe('');
        expect($component->get('password_confirmation'))->toBe('');
    });

    it('dispatches password-updated event on success', function () {
        $this->actingAs($this->user);

        livewire(PasswordPage::class)
            ->set('current_password', 'currentpassword')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('updatePassword')
            ->assertDispatched('password-updated');
    });
});
