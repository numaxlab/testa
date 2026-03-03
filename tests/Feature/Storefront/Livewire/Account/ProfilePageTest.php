<?php

use Illuminate\Support\Facades\Schema;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Storefront\Livewire\Account\ProfilePage;

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
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'password' => bcrypt('password'),
    ]);
    $userModel::reguard();

    $customer = \Lunar\Models\Customer::create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'company_name' => 'ACME Inc',
        'tax_identifier' => 'B12345678',
    ]);
    $customer->users()->attach($this->user);
    $this->customer = $customer;
});

describe('mount', function () {
    it('fills first_name from user', function () {
        $this->actingAs($this->user);

        $component = livewire(ProfilePage::class);

        expect($component->get('first_name'))->toBe('John');
    });

    it('fills last_name from user', function () {
        $this->actingAs($this->user);

        $component = livewire(ProfilePage::class);

        expect($component->get('last_name'))->toBe('Doe');
    });

    it('fills email from user', function () {
        $this->actingAs($this->user);

        $component = livewire(ProfilePage::class);

        expect($component->get('email'))->toBe('john@example.com');
    });

    it('fills tax_identifier from customer', function () {
        $this->actingAs($this->user);

        $component = livewire(ProfilePage::class);

        expect($component->get('tax_identifier'))->toBe('B12345678');
    });

    it('fills company_name from customer', function () {
        $this->actingAs($this->user);

        $component = livewire(ProfilePage::class);

        expect($component->get('company_name'))->toBe('ACME Inc');
    });
});

describe('updateProfileInformation', function () {
    it('validates first_name is required', function () {
        $this->actingAs($this->user);

        livewire(ProfilePage::class)
            ->set('first_name', '')
            ->call('updateProfileInformation')
            ->assertHasErrors(['first_name']);
    });

    it('validates last_name is required', function () {
        $this->actingAs($this->user);

        livewire(ProfilePage::class)
            ->set('last_name', '')
            ->call('updateProfileInformation')
            ->assertHasErrors(['last_name']);
    });

    it('validates email is required', function () {
        $this->actingAs($this->user);

        livewire(ProfilePage::class)
            ->set('email', '')
            ->call('updateProfileInformation')
            ->assertHasErrors(['email']);
    });

    it('validates email uniqueness ignoring current user', function () {
        $userModel = config('auth.providers.users.model');
        $userModel::unguard();
        $other = $userModel::create([
            'first_name' => 'Other',
            'last_name' => 'User',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
        ]);
        $userModel::reguard();

        $this->actingAs($this->user);

        livewire(ProfilePage::class)
            ->set('email', 'other@example.com')
            ->call('updateProfileInformation')
            ->assertHasErrors(['email']);
    });

    it('does not fail validation when email is the same as current user', function () {
        $this->actingAs($this->user);

        livewire(ProfilePage::class)
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john@example.com')
            ->call('updateProfileInformation')
            ->assertHasNoErrors();
    });

    it('updates user first_name and last_name', function () {
        $this->actingAs($this->user);

        livewire(ProfilePage::class)
            ->set('first_name', 'Jane')
            ->set('last_name', 'Smith')
            ->set('email', 'john@example.com')
            ->call('updateProfileInformation')
            ->assertHasNoErrors();

        $this->user->refresh();
        expect($this->user->first_name)->toBe('Jane');
        expect($this->user->last_name)->toBe('Smith');
    });

    it('updates customer tax_identifier and company_name', function () {
        $this->actingAs($this->user);

        livewire(ProfilePage::class)
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john@example.com')
            ->set('tax_identifier', 'C99999999')
            ->set('company_name', 'New Corp')
            ->call('updateProfileInformation')
            ->assertHasNoErrors();

        $this->customer->refresh();
        expect($this->customer->tax_identifier)->toBe('C99999999');
        expect($this->customer->company_name)->toBe('New Corp');
    });

    it('dispatches profile-updated event', function () {
        $this->actingAs($this->user);

        livewire(ProfilePage::class)
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john@example.com')
            ->call('updateProfileInformation')
            ->assertDispatched('profile-updated');
    });
});
