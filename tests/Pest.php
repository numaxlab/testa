<?php

use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;
use Lunar\Facades\CartSession;
use Lunar\Facades\StorefrontSession;
use Testa\Tests\TestCase;

pest()
    ->extend(TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

beforeEach(function () {
    DB::connection()->disableQueryLog();
});

afterEach(function () {
    // Clear session data
    session()->flush();

    // Clear Livewire state
    Livewire::flushState();

    // Clear Lunar facades state
    CartSession::forget();
    StorefrontSession::forget();

    // Clear Eloquent's resolved instance caches
    Model::clearBootedModels();

    // Force garbage collection
    gc_collect_cycles();
});
/**
 * Build an in-memory variant for the storefront availability component.
 */
function makeAvailabilityVariant(
    array $centres,
    int $stock = 1,
    string $mode = 'always',
    ?object $product = null,
): \NumaxLab\Lunar\Geslib\Models\ProductVariant {
    $variant = new \NumaxLab\Lunar\Geslib\Models\ProductVariant;
    $variant->stock = $stock;
    $variant->purchasable = $mode;

    if ($centres !== []) {
        $variant->attribute_data = [
            'stock-by-center' => new \Lunar\FieldTypes\ListField($centres),
        ];
    }

    $variant->setRelation('product', $product);

    return $variant;
}

/**
 * Build a purchasable whose fulfilment check is stubbed.
 */
function makeCartPurchasable(bool $canBeFulfilled): \Lunar\Base\Purchasable
{
    $purchasable = Mockery::mock(\Lunar\Base\Purchasable::class);
    $purchasable->shouldReceive('canBeFulfilledAtQuantity')->with(1)->andReturn($canBeFulfilled);

    return $purchasable;
}

/**
 * Build the Cegal availability service without hitting the API.
 */
function mockCegalAvailability(): \NumaxLab\Lunar\Geslib\Services\CegalAvailability
{
    $service = Mockery::mock(\NumaxLab\Lunar\Geslib\Services\CegalAvailability::class);
    $service->shouldReceive('getAvailability')->andReturnNull();

    return $service;
}
