<?php

use Illuminate\Support\Facades\Schema;
use Lunar\Models\Address;
use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Storefront\Livewire\Account\DashboardPage;

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

    $this->country = Country::factory()->create();

    $userModel = config('auth.providers.users.model');
    $userModel::unguard();
    $this->user = $userModel::create([
        'first_name' => 'Dash',
        'last_name' => 'User',
        'email' => 'dash@example.com',
        'password' => bcrypt('password'),
    ]);
    $userModel::reguard();

    $this->customer = \Lunar\Models\Customer::create([
        'first_name' => 'Dash',
        'last_name' => 'User',
    ]);
    $this->customer->users()->attach($this->user);

    $this->actingAs($this->user);
});

describe('mount', function () {
    it('sets user property', function () {
        $component = livewire(DashboardPage::class);

        expect($component->get('user.id'))->toBe($this->user->id);
    });

    it('sets customer property', function () {
        $component = livewire(DashboardPage::class);

        expect($component->get('customer.id'))->toBe($this->customer->id);
    });

    it('sets defaultAddress to null when no shipping_default address', function () {
        $component = livewire(DashboardPage::class);

        expect($component->get('defaultAddress'))->toBeNull();
    });

    it('sets defaultAddress when a shipping_default address exists', function () {
        Address::create([
            'customer_id' => $this->customer->id,
            'first_name' => 'Default',
            'last_name' => 'Address',
            'country_id' => $this->country->id,
            'postcode' => '28001',
            'city' => 'Madrid',
            'line_one' => 'Calle Default 1',
            'shipping_default' => true,
        ]);

        $component = livewire(DashboardPage::class);

        expect($component->get('defaultAddress'))->not->toBeNull();
        expect($component->get('defaultAddress.city'))->toBe('Madrid');
    });
});
