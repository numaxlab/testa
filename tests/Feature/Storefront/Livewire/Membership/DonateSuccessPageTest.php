<?php

use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Order;
use Lunar\Models\TaxClass;
use Testa\Storefront\Livewire\Membership\DonateSuccessPage;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->language = Language::factory()->create(['default' => true]);
    $this->currency = Currency::factory()->create(['default' => true, 'decimal_places' => 2, 'exchange_rate' => 1]);
    $this->channel = Channel::factory()->create(['default' => true]);
    $this->taxClass = TaxClass::factory()->create(['default' => true]);
    $this->customerGroup = CustomerGroup::factory()->create(['default' => true]);
});

describe('mount', function () {
    it('loads order when fingerprint matches and placed_at is set', function () {
        $order = Order::factory()->create([
            'fingerprint' => 'donate-fingerprint-456',
            'placed_at' => now(),
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);

        $component = livewire(DonateSuccessPage::class,
            ['id' => $order->id, 'fingerprint' => 'donate-fingerprint-456']);

        expect($component->get('order.id'))->toBe($order->id);
    });

    it('throws ModelNotFoundException when fingerprint not found', function () {
        livewire(DonateSuccessPage::class, ['id' => 99999, 'fingerprint' => 'nonexistent-fingerprint']);
    })->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

    it('throws ModelNotFoundException when placed_at is null', function () {
        $order = Order::factory()->create([
            'fingerprint' => 'unplaced-donate-fingerprint',
            'placed_at' => null,
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);

        livewire(DonateSuccessPage::class, ['id' => $order->id, 'fingerprint' => 'unplaced-donate-fingerprint']);
    })->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});
