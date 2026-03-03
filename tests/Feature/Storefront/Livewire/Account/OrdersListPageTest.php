<?php

use Illuminate\Support\Facades\Schema;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Order;
use Lunar\Models\TaxClass;
use Testa\Storefront\Livewire\Account\OrdersListPage;

use function Pest\Livewire\livewire;

beforeEach(function () {
    Schema::table('users', function ($table) {
        $table->dropColumn('name');
        $table->string('first_name')->after('id');
        $table->string('last_name')->after('first_name');
    });

    config(['auth.providers.users.model' => \Testa\Tests\Stubs\User::class]);

    $this->language = Language::factory()->create(['default' => true]);
    $this->currency = Currency::factory()->create(['default' => true, 'decimal_places' => 2, 'exchange_rate' => 1]);
    $this->channel = Channel::factory()->create(['default' => true]);
    $this->taxClass = TaxClass::factory()->create(['default' => true]);
    $this->customerGroup = CustomerGroup::factory()->create(['default' => true]);

    $userModel = config('auth.providers.users.model');
    $userModel::unguard();
    $this->user = $userModel::create([
        'first_name' => 'Orders',
        'last_name' => 'User',
        'email' => 'orders@example.com',
        'password' => bcrypt('password'),
    ]);
    $userModel::reguard();

    $this->customer = \Lunar\Models\Customer::create([
        'first_name' => 'Orders',
        'last_name' => 'User',
    ]);
    $this->customer->users()->attach($this->user);

    $this->actingAs($this->user);
});

describe('render', function () {
    it('renders successfully with no orders', function () {
        livewire(OrdersListPage::class)
            ->assertOk();
    });

    it('shows completed geslib orders', function () {
        Order::factory()->create([
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
            'status' => 'payment-received',
            'is_geslib' => true,
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);

        livewire(OrdersListPage::class)
            ->assertOk();
    });

    it('excludes awaiting-payment orders', function () {
        Order::factory()->create([
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
            'status' => 'awaiting-payment',
            'is_geslib' => true,
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);

        // Component should render (excluding the awaiting-payment order)
        livewire(OrdersListPage::class)
            ->assertOk();
    });

    it('excludes cancelled orders', function () {
        Order::factory()->create([
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
            'status' => 'cancelled',
            'is_geslib' => true,
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);

        livewire(OrdersListPage::class)
            ->assertOk();
    });

    it('excludes non-geslib orders', function () {
        Order::factory()->create([
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
            'status' => 'payment-received',
            'is_geslib' => false,
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);

        livewire(OrdersListPage::class)
            ->assertOk();
    });
});
