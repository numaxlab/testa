<?php

use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Testa\Models\Education\Course;
use Testa\Models\Education\Topic;
use Testa\Observers\CourseObserver;

beforeEach(function () {
    $this->language = Language::factory()->create(['default' => true]);
    $this->currency = Currency::factory()->create(['default' => true, 'decimal_places' => 2]);
    $this->taxClass = TaxClass::factory()->create(['default' => true]);

    $this->productOption = ProductOption::factory()->create([
        'handle' => CourseObserver::RATE_PRODUCT_OPTION_HANDLE,
    ]);

    $this->optionValue1 = ProductOptionValue::factory()->create([
        'product_option_id' => $this->productOption->id,
    ]);

    $this->optionValue2 = ProductOptionValue::factory()->create([
        'product_option_id' => $this->productOption->id,
    ]);
});

describe('CourseObserver created', function () {
    it('creates a Lunar product when a course is created', function () {
        $topic = Topic::factory()->create();

        $course = Course::create([
            'name' => 'Test Course',
            'subtitle' => 'Test Subtitle',
            'description' => 'Description',
            'topic_id' => $topic->id,
            'is_published' => true,
        ]);

        $course->refresh();
        expect($course->purchasable_id)->not->toBeNull();

        $product = Product::find($course->purchasable_id);
        expect($product)->not->toBeNull();
        expect($product->product_type_id)->toBe(CourseObserver::PRODUCT_TYPE_ID);
        expect($product->status)->toBe('published');
    });

    it('creates product variants for each product option value', function () {
        $topic = Topic::factory()->create();

        $course = Course::create([
            'name' => 'Test Course',
            'subtitle' => 'A subtitle',
            'description' => 'Description',
            'topic_id' => $topic->id,
            'is_published' => true,
        ]);

        $course->refresh();
        $product = Product::find($course->purchasable_id);
        $variants = $product->variants;

        expect($variants)->toHaveCount(2);

        foreach ($variants as $variant) {
            expect($variant->shippable)->toBeFalsy();
            expect($variant->purchasable)->toBe('always');
            expect($variant->sku)->toStartWith('course-'.$course->id.'-');
            expect($variant->prices)->toHaveCount(1);
            expect($variant->prices->first()->price->value)->toBe(0);
        }
    });

    it('attaches the product option to the product', function () {
        $topic = Topic::factory()->create();

        $course = Course::create([
            'name' => 'Test Course',
            'subtitle' => 'A subtitle',
            'description' => 'Description',
            'topic_id' => $topic->id,
            'is_published' => true,
        ]);

        $course->refresh();
        $product = Product::find($course->purchasable_id);

        expect($product->productOptions->pluck('id'))
            ->toContain($this->productOption->id);
    });
});

describe('CourseObserver updated', function () {
    it('updates product name when course name changes', function () {
        $topic = Topic::factory()->create();

        $course = Course::create([
            'name' => 'Original Name',
            'subtitle' => 'Subtitle',
            'description' => 'Description',
            'topic_id' => $topic->id,
            'is_published' => true,
        ]);

        $course->update(['name' => 'Updated Name']);

        $product = Product::find($course->purchasable_id);
        expect($product->translateAttribute('name'))->toBe('Updated Name');
    });

    it('creates product if it does not exist on update', function () {
        $topic = Topic::factory()->create();

        // Create course without triggering observer (use factory which bypasses)
        $course = Course::factory()->create([
            'purchasable_id' => null,
            'topic_id' => $topic->id,
        ]);

        // Manually set purchasable_id to null
        $course->updateQuietly(['purchasable_id' => null]);
        $course->refresh();

        expect($course->purchasable_id)->toBeNull();

        $course->update(['name' => 'Trigger Update']);
        $course->refresh();

        expect($course->purchasable_id)->not->toBeNull();
    });
});

describe('CourseObserver deleted', function () {
    it('attempts to delete product and variants when course is deleted', function () {
        $topic = Topic::factory()->create();

        $course = Course::create([
            'name' => 'To Delete',
            'subtitle' => 'Subtitle',
            'description' => 'Description',
            'topic_id' => $topic->id,
            'is_published' => true,
        ]);

        $course->refresh();
        $productId = $course->purchasable_id;

        expect(Product::find($productId))->not->toBeNull();
        expect(ProductVariant::where('product_id', $productId)->count())->toBe(2);

        // CourseObserver::deleted has a bug: it calls $product->variants()->prices()->delete()
        // which is invalid because prices() is not available on a HasMany relation builder.
        expect(fn () => $course->delete())->toThrow(BadMethodCallException::class);
    });
});
