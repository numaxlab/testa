<?php

use Illuminate\Support\Facades\Schema;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Order;
use Lunar\Models\TaxClass;
use Testa\Storefront\Livewire\Account\OrderPage;

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
        'first_name' => 'Order',
        'last_name' => 'User',
        'email' => 'orderpage@example.com',
        'password' => bcrypt('password'),
    ]);
    $userModel::reguard();

    $this->customer = \Lunar\Models\Customer::create([
        'first_name' => 'Order',
        'last_name' => 'User',
    ]);
    $this->customer->users()->attach($this->user);
});

describe('mount', function () {
    it('loads order by reference for authenticated customer', function () {
        $this->actingAs($this->user);

        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
            'status' => 'payment-received',
            'reference' => 'REF001',
            'is_geslib' => true,
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);

        $component = livewire(OrderPage::class, ['reference' => 'REF001']);

        expect($component->get('order.id'))->toBe($order->id);
        expect($component->get('order.reference'))->toBe('REF001');
    });

    it('throws ModelNotFoundException when reference not found', function () {
        $this->actingAs($this->user);

        livewire(OrderPage::class, ['reference' => 'NONEXISTENT']);
    })->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

    it('throws ModelNotFoundException for order belonging to different customer', function () {
        $userModel = config('auth.providers.users.model');
        $userModel::unguard();
        $otherUser = $userModel::create([
            'first_name' => 'Other',
            'last_name' => 'User',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
        ]);
        $userModel::reguard();

        $otherCustomer = \Lunar\Models\Customer::create([
            'first_name' => 'Other',
            'last_name' => 'User',
        ]);
        $otherCustomer->users()->attach($otherUser);

        // Order belongs to otherCustomer, not this->customer
        Order::factory()->create([
            'customer_id' => $otherCustomer->id,
            'user_id' => $otherUser->id,
            'status' => 'payment-received',
            'reference' => 'REF-OTHER',
            'is_geslib' => true,
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);

        $this->actingAs($this->user);

        livewire(OrderPage::class, ['reference' => 'REF-OTHER']);
    })->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

    it('throws ModelNotFoundException for awaiting-payment orders', function () {
        $this->actingAs($this->user);

        Order::factory()->create([
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
            'status' => 'awaiting-payment',
            'reference' => 'REF-AWAIT',
            'is_geslib' => true,
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);

        livewire(OrderPage::class, ['reference' => 'REF-AWAIT']);
    })->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

    it('throws ModelNotFoundException for cancelled orders', function () {
        $this->actingAs($this->user);

        Order::factory()->create([
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
            'status' => 'cancelled',
            'reference' => 'REF-CANCEL',
            'is_geslib' => true,
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);

        livewire(OrderPage::class, ['reference' => 'REF-CANCEL']);
    })->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});
