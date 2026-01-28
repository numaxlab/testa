<?php

namespace Testa\Observers;

use Illuminate\Support\Facades\DB;
use Lunar\FieldTypes\Text;
use Lunar\Models\Currency;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Testa\Models\Education\Course;

class CourseObserver
{
    public const int PRODUCT_TYPE_ID = 3;

    private const string DEFAULT_STATUS = 'published';

    public const string RATE_PRODUCT_OPTION_HANDLE = 'education-rate';

    public function created(Course $course): void
    {
        $this->createProduct($course);
    }

    private function createProduct(Course $course): void
    {
        $taxClass = TaxClass::where('default', true)->firstOrFail();
        $currency = Currency::where('default', true)->firstOrFail();

        DB::beginTransaction();

        $product = self::createProductFromCourse($course);

        $productOption = ProductOption::where('handle', self::RATE_PRODUCT_OPTION_HANDLE)
            ->with('values')
            ->firstOrFail();

        $product->productOptions()->attach($productOption, ['position' => 1]);

        foreach ($productOption->values as $optionValue) {
            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'tax_class_id' => $taxClass->id,
                'sku' => 'course-'.$course->id.'-'.$optionValue->id,
                'shippable' => false,
                'stock' => 0,
                'unit_quantity' => 1,
                'min_quantity' => 1,
                'quantity_increment' => 1,
                'backorder' => 0,
                'purchasable' => 'always',
            ]);

            $variant->values()->attach($optionValue);

            $variant->prices()->create([
                'price' => 0,
                'compare_price' => 0,
                'currency_id' => $currency->id,
                'min_quantity' => 1,
                'customer_group_id' => null,
            ]);
        }

        DB::commit();
    }

    public static function createProductFromCourse(Course $course): Product
    {
        $product = Product::create([
            'product_type_id' => self::PRODUCT_TYPE_ID,
            'status' => self::DEFAULT_STATUS,
            'attribute_data' => [
                'name' => new Text($course->name),
                'subtitle' => new Text($course->subtitle),
            ],
        ]);

        $course->updateQuietly(['purchasable_id' => $product->id]);

        return $product;
    }

    public function updated(Course $course): void
    {
        $product = $course->purchasable;

        if (! $product) {
            $this->createProduct($course);
            return;
        }

        if ($product->name !== $course->name) {
            $product->update([
                'attribute_data' => [
                    'name' => new Text($course->name),
                    'subtitle' => new Text($course->subtitle),
                ],
            ]);
        }
    }

    public function deleted(Course $course): void
    {
        $product = $course->purchasable;

        $product->variants()->prices()->delete();
        $product->variants()->delete();
        $product->delete();
    }
}
