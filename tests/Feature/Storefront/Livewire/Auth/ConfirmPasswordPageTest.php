<?php

use Illuminate\Support\Facades\Schema;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Storefront\Livewire\Auth\ConfirmPasswordPage;

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
        'first_name' => 'Confirm',
        'last_name' => 'User',
        'email' => 'confirm@example.com',
        'password' => bcrypt('correctpassword'),
    ]);
    $userModel::reguard();
});

describe('validation', function () {
    it('validates password is required', function () {
        $this->actingAs($this->user);

        livewire(ConfirmPasswordPage::class)
            ->set('password', '')
            ->call('confirmPassword')
            ->assertHasErrors(['password']);
    });
});

describe('confirmPassword', function () {
    it('throws validation error when password is wrong', function () {
        $this->actingAs($this->user);

        livewire(ConfirmPasswordPage::class)
            ->set('password', 'wrongpassword')
            ->call('confirmPassword')
            ->assertHasErrors(['password']);
    });

    it('sets password_confirmed_at session and redirects on correct password', function () {
        $this->actingAs($this->user);

        livewire(ConfirmPasswordPage::class)
            ->set('password', 'correctpassword')
            ->call('confirmPassword')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        expect(session()->get('auth.password_confirmed_at'))->not->toBeNull();
    });
});
