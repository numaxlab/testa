<?php

namespace Testa\Storefront\Queries\Bookshop;

use Illuminate\Database\Eloquent\Collection;
use Lunar\Models\Contracts\Product;
use Testa\Models\Editorial\Review;

final class GetProductReviews
{
    public function execute(Product $product): Collection
    {
        return Review::where('product_id', $product->id)->get();
    }
}
