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
use Testa\Storefront\Data\DonationData;
use Testa\Storefront\Queries\Membership\GetDonationProduct;
use Testa\Storefront\UseCases\Membership\PlaceDonation;
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
    $this->freeVariant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'sku' => GetDonationProduct::DONATION_SKU,
    ]);
    $this->paidVariant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'sku' => 'donation-10',
    ]);

    foreach ([$this->freeVariant, $this->paidVariant] as $variant) {
        Price::factory()->create([
            'currency_id' => $this->currency->id,
            'priceable_type' => $variant->getMorphClass(),
            'priceable_id' => $variant->id,
            'price' => 1000,
            'customer_group_id' => null,
        ]);
    }

    $this->product = $product->fresh(['variants']);

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

    $this->data = new DonationData(
        selectedQuantity: (string) $this->paidVariant->id,
        freeQuantityValue: null,
        paymentType: 'card',
        idNumber: '',
        comments: '',
    );
});

it('creates a cart for the user', function () {
    $cart = new PlaceDonation()->execute($this->user, $this->product, $this->data);

    expect($cart->user_id)->toBe($this->user->id);
});

it('sets the donation order type in meta', function () {
    $cart = new PlaceDonation()->execute($this->user, $this->product, $this->data);

    expect($cart->meta['Tipo de pedido'])->toBe('Donación');
});

it('adds the selected variant to the cart', function () {
    $cart = new PlaceDonation()->execute($this->user, $this->product, $this->data);

    expect($cart->fresh()->lines()->count())->toBe(1);
    expect($cart->fresh()->lines()->first()->purchasable_id)->toBe($this->paidVariant->id);
});

it('adds the donation sku variant with a custom unit price for free quantity', function () {
    $data = new DonationData(
        selectedQuantity: 'free',
        freeQuantityValue: 25.0,
        paymentType: 'card',
        idNumber: '',
        comments: '',
    );

    $cart = new PlaceDonation()->execute($this->user, $this->product, $data);

    $line = $cart->fresh()->lines()->first();
    expect($line->purchasable_id)->toBe($this->freeVariant->id);
    expect($line->meta['unit_price'])->toBe(2500);
});

it('sets a billing address on the cart', function () {
    $cart = new PlaceDonation()->execute($this->user, $this->product, $this->data);

    $billing = CartAddress::where('cart_id', $cart->id)->where('type', 'billing')->first();
    expect($billing)->not
        ->toBeNull()
        ->and($billing->city)->toBe('Madrid');
});
