<?php

namespace Trafikrak\Models\Editorial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lunar\Base\Traits\LogsActivity;
use Lunar\Models\Product;
use Spatie\Translatable\HasTranslations;

class Review extends Model
{
    use HasFactory;
    use HasTranslations;
    use LogsActivity;

    public $translatable = [
        'quote',
    ];
    protected $guarded = [];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::modelClass());
    }
}
