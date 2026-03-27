<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Lunar\Models\CartAddress;
use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\Customer as LunarCustomer;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Price;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Testa\Models\Education\Course;
use Testa\Storefront\Data\CheckoutAddressData;
use Testa\Storefront\Data\CourseRegistrationData;
use Testa\Storefront\UseCases\Education\RegisterForCourse;
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
    $this->country = Country::factory()->create(['iso2' => 'ES']);

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

    $product = Product::factory()->create();
    $this->variant = ProductVariant::factory()->create(['product_id' => $product->id]);
    Price::factory()->create([
        'currency_id' => $this->currency->id,
        'priceable_type' => $this->variant->getMorphClass(),
        'priceable_id' => $this->variant->id,
        'price' => 5000,
        'customer_group_id' => null,
    ]);

    $this->course = Course::withoutEvents(
        fn() => Course::factory()->create(['purchasable_id' => $product->id]),
    );

    $contactSettings = [
        ['name' => 'email_address', 'payload' => json_encode('info@example.com')],
        ['name' => 'phone_number', 'payload' => json_encode('+34 900 000 000')],
        [
            'name' => 'address', 'payload' => json_encode([
            [
                'is_primary' => true,
                'country_iso2' => 'ES',
                'city' => 'Madrid',
                'postcode' => '28001',
                'line_one' => 'Calle Mayor 1',
            ],
        ]),
        ],
        ['name' => 'instagram_url', 'payload' => json_encode(null)],
        ['name' => 'facebook_url', 'payload' => json_encode(null)],
        ['name' => 'x_url', 'payload' => json_encode(null)],
        ['name' => 'bluesky_url', 'payload' => json_encode(null)],
        ['name' => 'mastodon_url', 'payload' => json_encode(null)],
        ['name' => 'youtube_url', 'payload' => json_encode(null)],
        ['name' => 'vimeo_url', 'payload' => json_encode(null)],
        ['name' => 'soundcloud_url', 'payload' => json_encode(null)],
        ['name' => 'telegram_url', 'payload' => json_encode(null)],
        ['name' => 'whatsapp_url', 'payload' => json_encode(null)],
    ];
    foreach ($contactSettings as $setting) {
        \DB::table('settings')->insert(['group' => 'contact', 'locked' => false] + $setting);
    }

    $this->data = new CourseRegistrationData(
        selectedVariantId: (string) $this->variant->id,
        paymentType: 'card',
        invoice: false,
        billingAddress: null,
    );
});

it('creates a cart for the user', function () {
    $cart = new RegisterForCourse()->execute($this->user, $this->course, $this->data);

    expect($cart->user_id)->toBe($this->user->id);
});

it('sets the course order type in meta', function () {
    $cart = new RegisterForCourse()->execute($this->user, $this->course, $this->data);

    expect($cart->meta['Tipo de pedido'])->toBe('Curso');
});

it('sets factura to No when invoice is false', function () {
    $cart = new RegisterForCourse()->execute($this->user, $this->course, $this->data);

    expect($cart->meta['Factura'])->toBe('No');
});

it('sets factura to Si when invoice is true', function () {
    $billingAddress = new CheckoutAddressData(
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
    $data = new CourseRegistrationData(
        selectedVariantId: (string) $this->variant->id,
        paymentType: 'card',
        invoice: true,
        billingAddress: $billingAddress,
    );

    $cart = new RegisterForCourse()->execute($this->user, $this->course, $data);

    expect($cart->meta['Factura'])->toBe('Si');
});

it('adds the selected course variant to the cart', function () {
    $cart = new RegisterForCourse()->execute($this->user, $this->course, $this->data);

    expect($cart->fresh()->lines()->count())->toBe(1);
    expect($cart->fresh()->lines()->first()->purchasable_id)->toBe($this->variant->id);
});

it('sets billing address from form data when invoice is true', function () {
    $billingAddress = new CheckoutAddressData(
        first_name: 'Empresa SL',
        last_name: '',
        company_name: 'Empresa SL',
        tax_identifier: 'B12345678',
        contact_phone: null,
        contact_email: 'empresa@example.com',
        country_id: $this->country->id,
        state: null,
        postcode: '08001',
        city: 'Barcelona',
        line_one: 'Passeig de Gràcia 1',
        line_two: null,
    );
    $data = new CourseRegistrationData(
        selectedVariantId: (string) $this->variant->id,
        paymentType: 'card',
        invoice: true,
        billingAddress: $billingAddress,
    );

    $cart = new RegisterForCourse()->execute($this->user, $this->course, $data);

    $billing = CartAddress::where('cart_id', $cart->id)->where('type', 'billing')->first();
    expect($billing->city)
        ->toBe('Barcelona')
        ->and($billing->company_name)->toBe('Empresa SL');
});

it('sets billing address from contact settings when invoice is false', function () {
    $cart = new RegisterForCourse()->execute($this->user, $this->course, $this->data);

    $billing = CartAddress::where('cart_id', $cart->id)->where('type', 'billing')->first();
    expect($billing)->not
        ->toBeNull()
        ->and($billing->city)->toBe('Madrid');
});
