<?php

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Storefront\Livewire\Auth\RegisterPage;

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
});

describe('validation', function () {
    it('validates first_name is required', function () {
        livewire(RegisterPage::class)
            ->set('first_name', '')
            ->set('last_name', 'User')
            ->set('email', 'new@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('privacy_policy', '1')
            ->call('register')
            ->assertHasErrors(['first_name']);
    });

    it('validates last_name is required', function () {
        livewire(RegisterPage::class)
            ->set('first_name', 'Test')
            ->set('last_name', '')
            ->set('email', 'new@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('privacy_policy', '1')
            ->call('register')
            ->assertHasErrors(['last_name']);
    });

    it('validates email is required', function () {
        livewire(RegisterPage::class)
            ->set('first_name', 'Test')
            ->set('last_name', 'User')
            ->set('email', '')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('privacy_policy', '1')
            ->call('register')
            ->assertHasErrors(['email']);
    });

    it('validates email must be unique', function () {
        $userModel = config('auth.providers.users.model');
        $userModel::unguard();
        $userModel::create([
            'first_name' => 'Existing',
            'last_name' => 'User',
            'email' => 'existing@example.com',
            'password' => bcrypt('password'),
        ]);
        $userModel::reguard();

        livewire(RegisterPage::class)
            ->set('first_name', 'Test')
            ->set('last_name', 'User')
            ->set('email', 'existing@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('privacy_policy', '1')
            ->call('register')
            ->assertHasErrors(['email']);
    });

    it('validates password confirmation must match', function () {
        livewire(RegisterPage::class)
            ->set('first_name', 'Test')
            ->set('last_name', 'User')
            ->set('email', 'new@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'different')
            ->set('privacy_policy', '1')
            ->call('register')
            ->assertHasErrors(['password']);
    });

    it('validates privacy_policy must be accepted', function () {
        livewire(RegisterPage::class)
            ->set('first_name', 'Test')
            ->set('last_name', 'User')
            ->set('email', 'new@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('privacy_policy', '')
            ->call('register')
            ->assertHasErrors(['privacy_policy']);
    });
});

describe('successful registration', function () {
    it('creates user and customer then logs in', function () {
        $userModel = config('auth.providers.users.model');

        livewire(RegisterPage::class)
            ->set('first_name', 'New')
            ->set('last_name', 'User')
            ->set('email', 'newuser@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('privacy_policy', '1')
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        expect($userModel::where('email', 'newuser@example.com')->exists())->toBeTrue();
        expect(auth()->check())->toBeTrue();
        expect(auth()->user()->email)->toBe('newuser@example.com');
    });

    it('creates a customer record linked to the new user', function () {
        livewire(RegisterPage::class)
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('privacy_policy', '1')
            ->call('register');

        $user = config('auth.providers.users.model')::where('email', 'john@example.com')->first();
        expect($user->latestCustomer())->not->toBeNull();
        expect($user->latestCustomer()->first_name)->toBe('John');
        expect($user->latestCustomer()->last_name)->toBe('Doe');
    });

    it('dispatches Registered event on successful registration', function () {
        Event::fake([Registered::class]);

        livewire(RegisterPage::class)
            ->set('first_name', 'Event')
            ->set('last_name', 'User')
            ->set('email', 'event@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('privacy_policy', '1')
            ->call('register');

        Event::assertDispatched(Registered::class);
    });
});
