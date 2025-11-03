<?php

namespace Trafikrak\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Trafikrak\Models\Editorial\Review;

class Product extends \NumaxLab\Lunar\Geslib\Models\Product
{
    public function getTable()
    {
        return config('lunar.database.table_prefix').'products';
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'product_id');
    }
}
