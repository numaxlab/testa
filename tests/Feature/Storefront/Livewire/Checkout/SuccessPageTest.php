<?php

use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Order;
use Lunar\Models\TaxClass;
use Testa\Storefront\Livewire\Checkout\SuccessPage;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->language = Language::factory()->create(['default' => true]);
    $this->currency = Currency::factory()->create(['default' => true, 'decimal_places' => 2, 'exchange_rate' => 1]);
    $this->channel = Channel::factory()->create(['default' => true]);
    $this->taxClass = TaxClass::factory()->create(['default' => true]);
    $this->customerGroup = CustomerGroup::factory()->create(['default' => true]);
});

describe('mount', function () {
    it('loads order when fingerprint matches', function () {
        $order = Order::factory()->create([
            'fingerprint' => 'test-fingerprint-abc',
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
        ]);

        $component = livewire(SuccessPage::class, ['id' => $order->id, 'fingerprint' => 'test-fingerprint-abc']);

        expect($component->get('order')->id)->toBe($order->id);
    });

    it('throws ModelNotFoundException when fingerprint not found', function () {
        livewire(SuccessPage::class, ['id' => 99999, 'fingerprint' => 'nonexistent-fingerprint']);
    })->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

    it('has order property set with correct data after mount', function () {
        $order = Order::factory()->create([
            'fingerprint' => 'order-fingerprint-xyz',
            'currency_code' => $this->currency->code,
            'channel_id' => $this->channel->id,
            'status' => 'payment-received',
        ]);

        $component = livewire(SuccessPage::class, ['id' => $order->id, 'fingerprint' => 'order-fingerprint-xyz']);

        expect($component->get('order.status'))->toBe('payment-received');
    });
});
