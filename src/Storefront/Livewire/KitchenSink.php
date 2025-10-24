<?php

namespace Trafikrak\Storefront\Livewire;

use Faker\Factory as FakerFactory;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Lunar\Facades\StorefrontSession;
use Lunar\FieldTypes\Text as TextAttribute;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\ProductVariant;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Trafikrak\Models\Content\Banner;
use Trafikrak\Models\Education\Course;
use Trafikrak\Models\Education\Topic;

class KitchenSink extends Page
{
    public Collection $products;
    public Banner $banner;
    public Collection $courses;

    public function mount(): void
    {
        $this->getProducts();
        $this->getBanner();
        $this->getCourses();
    }

    private function getProducts(): void
    {
        $this->products = Product::query()
            ->inRandomOrder()
            ->take(6)
            ->get();

        if ($this->products->count() < 6) {
            for ($i = 0; $i < 6; $i++) {
                $product = Product::factory()
                    ->for(ProductType::factory())
                    ->create([
                        'attribute_data' => collect([
                            'name' => new TextAttribute(FakerFactory::create()->sentence(rand(5, 10))),
                        ]),
                        'status' => 'published',
                    ]);

                $product->scheduleCustomerGroup(StorefrontSession::getCustomerGroups());

                $variant = ProductVariant::factory()->create([
                    'product_id' => $product->id,
                ]);

                $variant->prices()->create([
                    'price' => FakerFactory::create()->randomNumber(4),
                    'currency_id' => StorefrontSession::getCurrency()->id,
                ]);

                $product = $product->refresh();

                $this->products->push($product);
            }
        }
    }

    private function getBanner(): void
    {
        $this->banner = Banner::query()
            ->inRandomOrder()
            ->first();

        if (!$this->banner) {
            $this->banner = Banner::factory()->create();
        }
    }

    private function getCourses(): void
    {
        $this->courses = Course::query()
            ->with('topic')
            ->inRandomOrder()
            ->limit(4)
            ->get();

        if ($this->courses->count() < 4) {
            $topic = Topic::factory()->create();

            for ($i = 0; $i < 4; $i++) {
                $course = Course::factory()->create([
                    'topic_id' => $topic->id,
                ]);

                $this->courses->push($course);
            }
        }
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.kitchen-sink');
    }

}
