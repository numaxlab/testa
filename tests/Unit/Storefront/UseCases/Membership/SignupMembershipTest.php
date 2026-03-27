<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Lunar\Models\CartAddress;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\Customer as LunarCustomer;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Price;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Testa\Models\Membership\MembershipPlan;
use Testa\Storefront\Data\CheckoutAddressData;
use Testa\Storefront\Data\MembershipSignupData;
use Testa\Storefront\UseCases\Membership\SignupMembership;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Schema::table('users', function ($table) {
        $table->dropColumn('name');
        $table->string('first_name')->after('id');
        $table->string('last_name')->after('first_name');
    });

    config(['auth.providers.users.model' => \Testa\Tests\Stubs\User::class]);

    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    $this->currency = Currency::factory()->create(['default' => true, 'decimal_places' => 2]);
    Channel::factory()->create(['default' => true]);
    CustomerGroup::factory()->create(['default' => true]);
    $this->country = \Lunar\Models\Country::factory()->create(['iso2' => 'ES']);

    $userModel = config('auth.providers.users.model');
    $userModel::unguard();
    $this->user = $userModel::create([
        'first_name' => 'Ana',
        'last_name' => 'García',
        'email' => 'ana@example.com',
        'password' => bcrypt('password'),
    ]);
    $userModel::reguard();

    $lunarCustomer = LunarCustomer::create([
        'first_name' => 'Ana',
        'last_name' => 'García',
    ]);
    $lunarCustomer->users()->attach($this->user);

    $variant = ProductVariant::factory()->create();
    Price::factory()->create([
        'currency_id' => $this->currency->id,
        'priceable_type' => $variant->getMorphClass(),
        'priceable_id' => $variant->id,
        'price' => 1000,
        'customer_group_id' => null,
    ]);

    $this->plan = MembershipPlan::factory()->create(['variant_id' => $variant->id]);

    $this->billingAddress = new CheckoutAddressData(
        first_name: 'Ana',
        last_name: 'García',
        company_name: null,
        tax_identifier: null,
        contact_phone: null,
        contact_email: 'ana@example.com',
        country_id: $this->country->id,
        state: null,
        postcode: '28001',
        city: 'Madrid',
        line_one: 'Calle Mayor 1',
        line_two: null,
    );

    $this->data = new MembershipSignupData(
        membershipPlanId: $this->plan->id,
        paymentType: 'card',
        idNumber: 'DNI12345',
        directDebitOwnerName: null,
        directDebitBankName: null,
        directDebitIban: null,
        billingAddress: $this->billingAddress,
    );
});

it('creates a cart for the user', function () {
    $cart = new SignupMembership()->execute($this->user, $this->data);

    expect($cart->user_id)->toBe($this->user->id);
});

it('sets the membership order type in meta', function () {
    $cart = new SignupMembership()->execute($this->user, $this->data);

    expect($cart->meta['Tipo de pedido'])->toBe('Subscripción socias');
});

it('sets the id number in meta', function () {
    $cart = new SignupMembership()->execute($this->user, $this->data);

    expect($cart->meta['DNI/NIF'])->toBe('DNI12345');
});

it('includes direct-debit fields in meta when payment type is direct-debit', function () {
    $data = new MembershipSignupData(
        membershipPlanId: $this->plan->id,
        paymentType: 'direct-debit',
        idNumber: 'DNI12345',
        directDebitOwnerName: 'Ana García',
        directDebitBankName: 'Banco de España',
        directDebitIban: 'ES9121000418450200051332',
        billingAddress: $this->billingAddress,
    );

    $cart = new SignupMembership()->execute($this->user, $data);

    expect($cart->meta['Titular de la cuenta'])
        ->toBe('Ana García')
        ->and($cart->meta['Banco'])->toBe('Banco de España')
        ->and($cart->meta['IBAN'])->toBe('ES9121000418450200051332');
});

it('does not include direct-debit fields in meta for other payment types', function () {
    $cart = new SignupMembership()->execute($this->user, $this->data);

    expect($cart->meta)->not
        ->toHaveKey('Titular de la cuenta')
        ->and($cart->meta)->not->toHaveKey('IBAN');
});

it('adds the membership plan variant to the cart', function () {
    $cart = new SignupMembership()->execute($this->user, $this->data);

    expect($cart->fresh()->lines()->count())->toBe(1);
});

it('sets the billing address on the cart', function () {
    $cart = new SignupMembership()->execute($this->user, $this->data);

    $billing = CartAddress::where('cart_id', $cart->id)->where('type', 'billing')->first();
    expect($billing)->not
        ->toBeNull()
        ->and($billing->first_name)->toBe('Ana')
        ->and($billing->city)->toBe('Madrid');
});
