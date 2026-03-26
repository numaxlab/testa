<?php

namespace Testa\Storefront\Queries\Bookshop;

use Illuminate\Database\Eloquent\Collection;
use Lunar\Models\Contracts\Product;
use Testa\Models\Attachment;
use Testa\Models\Education\CourseModule;
use Testa\Models\News\Event;

final class GetProductAttachments
{
    public function execute(Product $product): Collection
    {
        return Attachment::where(function ($query) use ($product) {
            $query->where(function ($query) use ($product) {
                $query
                    ->where('attachable_type', (new Event)->getMorphClass())
                    ->whereIn(
                        'attachable_id',
                        Event::whereHas('products', fn($query) => $query->where('product_id', $product->id))
                            ->where('is_published', true)
                            ->get()
                            ->pluck('id')
                            ->toArray(),
                    );
            })->orWhere(function ($query) use ($product) {
                $query
                    ->where('attachable_type', (new CourseModule)->getMorphClass())
                    ->whereIn(
                        'attachable_id',
                        CourseModule::whereHas('products', fn($query) => $query->where('product_id', $product->id))
                            ->where('is_published', true)
                            ->get()
                            ->pluck('id')
                            ->toArray(),
                    );
            });
        })->whereHas('media', fn($query) => $query->where('is_published', true))
            ->with('media')
            ->get();
    }
}
