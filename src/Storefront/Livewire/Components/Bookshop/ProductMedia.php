<?php

namespace Trafikrak\Storefront\Livewire\Components\Bookshop;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Contracts\Product;
use Trafikrak\Models\Attachment;
use Trafikrak\Models\Education\CourseModule;
use Trafikrak\Models\News\Event;

class ProductMedia extends Component
{
    public Product $product;

    public Collection $attachments;

    public function mount(): void
    {
        $this->attachments = Attachment::where(function ($query) {
            $query->where(function ($query) {
                $query
                    ->where('attachable_type', (new Event)->getMorphClass())
                    ->whereIn(
                        'attachable_id',
                        Event::whereHas('products', fn ($query) => $query->where('product_id', $this->product->id))
                            ->where('is_published', true)
                            ->get()
                            ->pluck('id')
                            ->toArray(),
                    );
            })->orWhere(function ($query) {
                $query
                    ->where('attachable_type', (new CourseModule)->getMorphClass())
                    ->whereIn(
                        'attachable_id',
                        CourseModule::whereHas(
                            'products',
                            fn ($query) => $query->where('product_id', $this->product->id),
                        )
                            ->where('is_published', true)
                            ->get()
                            ->pluck('id')
                            ->toArray(),
                    );
            });
        })->whereHas('media', fn ($query) => $query->where('is_published', true))
            ->with('media')
            ->get();
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.components.bookshop.product-media');
    }
}
