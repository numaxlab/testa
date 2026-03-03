<?php

use Illuminate\Support\Facades\Schema;
use Lunar\Models\Address;
use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Storefront\Livewire\Account\HandleAddressPage;

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
        'first_name' => 'Addr',
        'last_name' => 'User',
        'email' => 'addr@example.com',
        'password' => bcrypt('password'),
    ]);
    $userModel::reguard();

    $this->customer = \Lunar\Models\Customer::create([
        'first_name' => 'Addr',
        'last_name' => 'User',
        'company_name' => 'ACME Ltd',
        'tax_identifier' => 'A99999999',
    ]);
    $this->customer->users()->attach($this->user);

    $this->actingAs($this->user);
});

describe('mount for new address', function () {
    it('loads countries on mount', function () {
        $component = livewire(HandleAddressPage::class);

        expect($component->get('form.countries'))->not->toBeEmpty();
    });

    it('pre-fills first_name and last_name from user on new address', function () {
        $component = livewire(HandleAddressPage::class);

        expect($component->get('form.first_name'))->toBe('Addr');
        expect($component->get('form.last_name'))->toBe('User');
    });

    it('pre-fills company_name from customer on new address', function () {
        $component = livewire(HandleAddressPage::class);

        expect($component->get('form.company_name'))->toBe('ACME Ltd');
    });
});

describe('mount for existing address', function () {
    it('loads existing address data', function () {
        $address = Address::create([
            'customer_id' => $this->customer->id,
            'first_name' => 'Saved',
            'last_name' => 'Address',
            'country_id' => $this->country->id,
            'postcode' => '28001',
            'city' => 'Madrid',
            'line_one' => 'Calle Test 1',
        ]);

        $component = livewire(HandleAddressPage::class, ['id' => $address->id]);

        expect($component->get('form.first_name'))->toBe('Saved');
        expect($component->get('form.last_name'))->toBe('Address');
        expect($component->get('form.city'))->toBe('Madrid');
    });

    it('throws ModelNotFoundException for address belonging to another customer', function () {
        $otherCustomer = \Lunar\Models\Customer::create([
            'first_name' => 'Other',
            'last_name' => 'Customer',
        ]);
        $otherAddress = Address::create([
            'customer_id' => $otherCustomer->id,
            'first_name' => 'Other',
            'last_name' => 'Address',
            'country_id' => $this->country->id,
            'postcode' => '28001',
            'city' => 'Madrid',
            'line_one' => 'Calle Other 1',
        ]);

        livewire(HandleAddressPage::class, ['id' => $otherAddress->id]);
    })->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

describe('save', function () {
    it('validates required form fields', function () {
        livewire(HandleAddressPage::class)
            ->set('form.first_name', '')
            ->set('form.last_name', '')
            ->set('form.country_id', null)
            ->set('form.postcode', '')
            ->set('form.city', '')
            ->set('form.line_one', '')
            ->call('save')
            ->assertHasErrors([
                'form.first_name',
                'form.last_name',
                'form.country_id',
                'form.postcode',
                'form.city',
                'form.line_one',
            ]);
    });

    it('saves new address and redirects to dashboard', function () {
        livewire(HandleAddressPage::class)
            ->set('form.first_name', 'New')
            ->set('form.last_name', 'Address')
            ->set('form.country_id', $this->country->id)
            ->set('form.state', 'Madrid')
            ->set('form.postcode', '28001')
            ->set('form.city', 'Madrid')
            ->set('form.line_one', 'Calle Nueva 1')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        expect(
            Address::where('customer_id', $this->customer->id)
                ->where('first_name', 'New')
                ->exists(),
        )->toBeTrue();
    });
});

describe('updated hook', function () {
    it('loads states when country_id changes', function () {
        $component = livewire(HandleAddressPage::class)
            ->set('form.country_id', $this->country->id);

        // states collection is set (possibly empty if no states for this country)
        expect($component->get('form.states'))->toBeInstanceOf(\Illuminate\Support\Collection::class);
    });
});
