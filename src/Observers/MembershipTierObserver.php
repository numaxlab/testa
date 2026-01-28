<?php

namespace Testa\Observers;

use Illuminate\Support\Facades\DB;
use Lunar\FieldTypes\Text;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Testa\Models\Membership\MembershipTier;

class MembershipTierObserver
{
    public const int PRODUCT_TYPE_ID = 4;

    private const string DEFAULT_STATUS = 'published';

    public function created(MembershipTier $tier): void
    {
        $this->createProduct($tier);
    }

    private function createProduct(MembershipTier $tier): void
    {
        DB::beginTransaction();

        $product = Product::create([
            'product_type_id' => self::PRODUCT_TYPE_ID,
            'status' => self::DEFAULT_STATUS,
            'attribute_data' => [
                'name' => new Text($tier->name),
            ],
        ]);

        $productOption = ProductOption::create([
            'handle' => 'membership-tier-'.$product->id.'-plans',
            'name' => [
                'es' => 'Opciones de '.$tier->name,
            ],
            'label' => [
                'es' => 'Opciones de '.$tier->name,
            ],
            'shared' => false,
        ]);

        $product->productOptions()->attach($productOption->id, [
            'position' => 1,
        ]);

        $tier->updateQuietly(['purchasable_id' => $product->id]);

        DB::commit();
    }

    public function updated(MembershipTier $tier): void
    {
        $product = $tier->purchasable;

        if (! $product) {
            $this->createProduct($tier);

            return;
        }

        if ($product->translateAttribute('name') !== $tier->name) {
            $product->update([
                'attribute_data' => [
                    'name' => new Text($tier->name),
                ],
            ]);
        }
    }

    public function deleted(MembershipTier $tier): void
    {
        $product = $tier->purchasable;

        if (! $product) {
            return;
        }

        foreach ($product->variants as $variant) {
            $variant->prices()->delete();
            $variant->delete();
        }

        $product->delete();
    }
}
