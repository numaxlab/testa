<?php

use Lunar\Facades\CartSession;
use Lunar\Models\Cart;
use Lunar\Models\CartLine;
use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\Price;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Lunar\Models\TaxRate;
use Lunar\Models\TaxRateAmount;
use Lunar\Models\TaxZone;
use Lunar\Models\TaxZoneCountry;
use Lunar\Models\Url;
use Testa\Storefront\Livewire\Components\Cart as CartComponent;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->language = Language::factory()->create(['default' => true]);
    $this->currency = Currency::factory()->create(['default' => true, 'decimal_places' => 2]);
    $this->channel = Channel::factory()->create(['default' => true]);
    $this->taxClass = TaxClass::factory()->create(['default' => true]);

    // Set up tax zone with country and tax rate
    $this->country = Country::factory()->create();
    $this->taxZone = TaxZone::factory()->create([
        'default' => true,
        'zone_type' => 'country',
    ]);
    TaxZoneCountry::factory()->create([
        'tax_zone_id' => $this->taxZone->id,
        'country_id' => $this->country->id,
    ]);
    $this->taxRate = TaxRate::factory()->create([
        'tax_zone_id' => $this->taxZone->id,
    ]);
    TaxRateAmount::factory()->create([
        'tax_rate_id' => $this->taxRate->id,
        'tax_class_id' => $this->taxClass->id,
        'percentage' => 21,
    ]);
});

function createCartWithLines(int $lineCount = 1): Cart
{
    $currency = Currency::getDefault();
    $channel = Channel::getDefault();
    $language = Language::getDefault();
    $taxClass = TaxClass::getDefault();

    $cart = Cart::factory()->create([
        'currency_id' => $currency->id,
        'channel_id' => $channel->id,
    ]);

    for ($i = 0; $i < $lineCount; $i++) {
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'tax_class_id' => $taxClass->id,
        ]);

        Url::factory()->create([
            'element_type' => Product::morphName(),
            'element_id' => $product->id,
            'default' => true,
            'language_id' => $language->id,
        ]);

        Price::factory()->create([
            'priceable_type' => ProductVariant::morphName(),
            'priceable_id' => $variant->id,
            'currency_id' => $currency->id,
            'min_quantity' => 1,
            'price' => 1000,
        ]);

        CartLine::factory()->create([
            'cart_id' => $cart->id,
            'purchasable_type' => ProductVariant::morphName(),
            'purchasable_id' => $variant->id,
            'quantity' => 1,
        ]);
    }

    return $cart->fresh(['lines.purchasable.product.urls', 'lines.purchasable.prices']);
}

describe('Cart component initialization', function () {
    it('initializes with empty lines array when no cart exists', function () {
        $component = new CartComponent();
        $component->mount();

        expect($component->lines)->toBe([]);
        expect($component->linesVisible)->toBeFalse();
    });

    it('initializes linesVisible as false', function () {
        $component = new CartComponent();
        $component->mount();

        expect($component->linesVisible)->toBeFalse();
    });

    it('has correct validation rules', function () {
        $component = new CartComponent();
        $rules = $component->rules();

        expect($rules)->toHaveKey('lines.*.quantity');
        expect($rules['lines.*.quantity'])->toBe('required|numeric|min:1|max:100');
    });

    it('has add-to-cart listener configured', function () {
        $component = new CartComponent();

        // Access the protected $listeners property
        $reflection = new ReflectionClass($component);
        $property = $reflection->getProperty('listeners');
        $listeners = $property->getValue($component);

        expect($listeners)->toHaveKey('add-to-cart');
        expect($listeners['add-to-cart'])->toBe('handleAddToCart');
    });
});

describe('Cart computed properties', function () {
    it('returns null cart when no cart exists in session', function () {
        $component = new CartComponent();
        $component->mount();

        expect($component->cart)->toBeNull();
    });

    it('returns cart when cart exists in session', function () {
        $cart = createCartWithLines(1);
        CartSession::use($cart);

        $component = new CartComponent();
        $component->mount();

        expect($component->cart)->not->toBeNull();
        expect($component->cart->id)->toBe($cart->id);
    });

    it('returns empty collection for cartLines when no cart', function () {
        $component = new CartComponent();
        $component->mount();

        expect($component->cartLines)->toBeEmpty();
    });

    it('returns cart lines when cart has items', function () {
        $cart = createCartWithLines(2);
        CartSession::use($cart);

        $component = new CartComponent();
        $component->mount();

        expect($component->cartLines)->toHaveCount(2);
    });
});

describe('Cart line mapping', function () {
    it('maps cart lines to array with correct structure', function () {
        $cart = createCartWithLines(1);
        CartSession::use($cart);

        $component = new CartComponent();
        $component->mount();

        expect($component->lines)->toHaveCount(1);
        expect($component->lines[0])->toHaveKeys([
            'id',
            'slug',
            'quantity',
            'description',
            'thumbnail',
            'sub_total',
            'unit_price',
        ]);
    });

    it('maps multiple cart lines', function () {
        $cart = createCartWithLines(3);
        CartSession::use($cart);

        $component = new CartComponent();
        $component->mount();

        expect($component->lines)->toHaveCount(3);
    });

    it('maps line id correctly', function () {
        $cart = createCartWithLines(1);
        CartSession::use($cart);

        $component = new CartComponent();
        $component->mount();

        $expectedLineId = $cart->lines->first()->id;
        expect($component->lines[0]['id'])->toBe($expectedLineId);
    });

    it('maps line quantity correctly', function () {
        $cart = createCartWithLines(1);
        $cart->lines->first()->update(['quantity' => 5]);
        $cart->refresh();
        CartSession::use($cart);

        $component = new CartComponent();
        $component->mount();

        expect($component->lines[0]['quantity'])->toBe(5);
    });

    it('forgets cart session when cart is empty after mapping', function () {
        $cart = Cart::factory()->create([
            'currency_id' => Currency::getDefault()->id,
            'channel_id' => Channel::getDefault()->id,
        ]);
        CartSession::use($cart);

        $component = new CartComponent();
        $component->mount();

        expect($component->lines)->toBe([]);
        expect(CartSession::current())->toBeNull();
    });
});

describe('Cart line updates via Livewire', function () {
    it('validates quantity is required', function () {
        $cart = createCartWithLines(1);
        CartSession::use($cart);

        livewire(CartComponent::class)
            ->set('lines.0.quantity', null)
            ->call('updateLines')
            ->assertHasErrors(['lines.0.quantity' => 'required']);
    });

    it('validates quantity is numeric', function () {
        $cart = createCartWithLines(1);
        CartSession::use($cart);

        livewire(CartComponent::class)
            ->set('lines.0.quantity', 'not-a-number')
            ->call('updateLines')
            ->assertHasErrors(['lines.0.quantity' => 'numeric']);
    });

    it('validates minimum quantity of 1', function () {
        $cart = createCartWithLines(1);
        CartSession::use($cart);

        livewire(CartComponent::class)
            ->set('lines.0.quantity', 0)
            ->call('updateLines')
            ->assertHasErrors(['lines.0.quantity' => 'min']);
    });

    it('validates maximum quantity of 100', function () {
        $cart = createCartWithLines(1);
        CartSession::use($cart);

        livewire(CartComponent::class)
            ->set('lines.0.quantity', 101)
            ->call('updateLines')
            ->assertHasErrors(['lines.0.quantity' => 'max']);
    });

    it('accepts valid quantity', function () {
        $cart = createCartWithLines(1);
        CartSession::use($cart);

        livewire(CartComponent::class)
            ->set('lines.0.quantity', 50)
            ->call('updateLines')
            ->assertHasNoErrors();
    });

    it('dispatches cartUpdated event on successful update', function () {
        $cart = createCartWithLines(1);
        CartSession::use($cart);

        livewire(CartComponent::class)
            ->set('lines.0.quantity', 3)
            ->call('updateLines')
            ->assertDispatched('cartUpdated');
    });

    it('updates cart line quantity in database', function () {
        $cart = createCartWithLines(1);
        CartSession::use($cart);
        $lineId = $cart->lines->first()->id;

        livewire(CartComponent::class)
            ->set('lines.0.quantity', 7)
            ->call('updateLines');

        $updatedLine = CartLine::find($lineId);
        expect($updatedLine->quantity)->toBe(7);
    });
});

describe('Cart line removal', function () {
    it('removes a cart line', function () {
        $cart = createCartWithLines(2);
        CartSession::use($cart);
        $lineToRemove = $cart->lines->first();
        $lineToKeep = $cart->lines->last();

        livewire(CartComponent::class)
            ->call('removeLine', $lineToRemove->id);

        expect(CartLine::find($lineToRemove->id))->toBeNull();
        expect(CartLine::find($lineToKeep->id))->not->toBeNull();
    });

    it('updates lines array after removal', function () {
        $cart = createCartWithLines(2);
        CartSession::use($cart);

        $component = livewire(CartComponent::class);
        expect($component->get('lines'))->toHaveCount(2);

        $lineId = $cart->lines->first()->id;
        $component->call('removeLine', $lineId);

        expect($component->get('lines'))->toHaveCount(1);
    });

    it('forgets cart session when last line is removed', function () {
        $cart = createCartWithLines(1);
        CartSession::use($cart);

        livewire(CartComponent::class)
            ->call('removeLine', $cart->lines->first()->id);

        expect(CartSession::current())->toBeNull();
    });
});

describe('Add to cart event handling', function () {
    it('sets linesVisible to true on add-to-cart event', function () {
        $cart = createCartWithLines(1);
        CartSession::use($cart);

        livewire(CartComponent::class)
            ->assertSet('linesVisible', false)
            ->dispatch('add-to-cart')
            ->assertSet('linesVisible', true);
    });

    it('refreshes lines on add-to-cart event', function () {
        // Start with an empty cart, then add lines externally
        $cart = Cart::factory()->create([
            'currency_id' => Currency::getDefault()->id,
            'channel_id' => Channel::getDefault()->id,
        ]);
        CartSession::use($cart);

        // Component starts with no lines
        $component = livewire(CartComponent::class);
        expect($component->get('lines'))->toHaveCount(0);

        // Now create a full cart with lines and use it (simulating what AddToCart does)
        $cartWithLines = createCartWithLines(2);
        CartSession::use($cartWithLines);

        // Dispatch the event - component should refresh and see the new cart's lines
        $component->dispatch('add-to-cart');
        expect($component->get('lines'))->toHaveCount(2);
    });
});

describe('Edge cases', function () {
    it('handles cart with no lines gracefully', function () {
        $cart = Cart::factory()->create([
            'currency_id' => Currency::getDefault()->id,
            'channel_id' => Channel::getDefault()->id,
        ]);
        CartSession::use($cart);

        $component = new CartComponent();
        $component->mount();

        expect($component->lines)->toBe([]);
    });

    it('validates all lines when multiple exist', function () {
        $cart = createCartWithLines(2);
        CartSession::use($cart);

        livewire(CartComponent::class)
            ->set('lines.0.quantity', 0)
            ->set('lines.1.quantity', 101)
            ->call('updateLines')
            ->assertHasErrors([
                'lines.0.quantity' => 'min',
                'lines.1.quantity' => 'max',
            ]);
    });

    it('removes correct line when multiple exist', function () {
        $cart = createCartWithLines(3);
        CartSession::use($cart);

        $lines = $cart->lines;
        $middleLineId = $lines[1]->id;

        livewire(CartComponent::class)
            ->call('removeLine', $middleLineId);

        expect(CartLine::find($middleLineId))->toBeNull();
        expect(CartLine::find($lines[0]->id))->not->toBeNull();
        expect(CartLine::find($lines[2]->id))->not->toBeNull();
    });
});
