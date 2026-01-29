<?php

namespace Testa\Models\Editorial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lunar\Base\Traits\LogsActivity;
use Lunar\Models\Product;
use Spatie\Translatable\HasTranslations;
use Testa\Database\Factories\Editorial\ReviewFactory;

class Review extends Model
{
    use HasFactory;
    use HasTranslations;
    use LogsActivity;

    protected static function newFactory()
    {
        return ReviewFactory::new();
    }

    public $translatable = [
        'quote',
    ];
    protected $guarded = [];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::modelClass());
    }
}
