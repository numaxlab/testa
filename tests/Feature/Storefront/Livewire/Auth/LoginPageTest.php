<?php

use Illuminate\Support\Facades\Schema;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Storefront\Livewire\Auth\LoginPage;

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
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
    $userModel::reguard();
});

describe('validation', function () {
    it('validates email is required', function () {
        livewire(LoginPage::class)
            ->set('email', '')
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors(['email']);
    });

    it('validates password is required', function () {
        livewire(LoginPage::class)
            ->set('email', 'test@example.com')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors(['password']);
    });

    it('validates email must be a valid email', function () {
        livewire(LoginPage::class)
            ->set('email', 'not-an-email')
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors(['email']);
    });
});

describe('authentication', function () {
    it('throws validation error with wrong credentials', function () {
        livewire(LoginPage::class)
            ->set('email', 'test@example.com')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['email']);
    });

    it('logs in successfully with correct credentials', function () {
        livewire(LoginPage::class)
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->call('login')
            ->assertHasNoErrors();

        expect(auth()->check())->toBeTrue();
        expect(auth()->user()->email)->toBe('test@example.com');
    });

    it('redirects to dashboard after successful login', function () {
        livewire(LoginPage::class)
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('dashboard', absolute: false));
    });
});
